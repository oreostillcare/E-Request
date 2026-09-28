<?php

require_once __DIR__ . '/bootstrap.php';

class class_model
{
    private SupabaseClient $db;
    public string $error = '';

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    private function attempt(callable $operation, mixed $fallback = false): mixed
    {
        try {
            return $operation();
        } catch (Throwable $error) {
            $this->error = $error->getMessage();
            error_log($this->error);
            return $fallback;
        }
    }

    private function passwordMatches(string $entered, string $stored): bool
    {
        if (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon2')) {
            return password_verify($entered, $stored);
        }
        return hash_equals($stored, $entered);
    }

    private function countRows(string $table, array $filters = []): int
    {
        return count($this->db->select($table, $filters, ['select' => '*']));
    }

    public function login_student($username, $password, $status): array
    {
        return $this->attempt(function () use ($username, $password, $status) {
            $rows = $this->db->select('tbl_student', [
                'username' => 'eq.' . $username,
                'account_status' => 'eq.' . $status,
            ], ['limit' => 1]);
            $student = $rows[0] ?? null;
            $valid = is_array($student) && $this->passwordMatches((string) $password, (string) $student['password']);
            return ['student_id' => $valid ? (int) $student['student_id'] : 0, 'count' => $valid ? 1 : 0];
        }, ['student_id' => 0, 'count' => 0]);
    }

    public function student_account($student_id): array
    {
        return $this->attempt(function () use ($student_id) {
            $rows = $this->db->select('tbl_student', ['student_id' => 'eq.' . (int) $student_id], ['limit' => 1]);
            $student = $rows[0] ?? [];
            return [
                'first_name' => $student['first_name'] ?? '',
                'last_name' => $student['last_name'] ?? '',
                'profile_photo_url' => $this->student_profile_photo_url($student_id),
            ];
        }, ['first_name' => '', 'last_name' => '', 'profile_photo_url' => '']);
    }

    public function student_profile($student_id): array
    {
        return $this->attempt(function () use ($student_id) {
            $rows = $this->db->select('tbl_student', ['student_id' => 'eq.' . (int) $student_id], ['limit' => 1]);
            return $rows[0] ?? [];
        }, []);
    }

    public function student_profile_photo_url($student_id): string
    {
        return $this->db->publicStorageUrl(
            'profile-images',
            'students/' . (int) $student_id . '/avatar'
        );
    }

    public function upload_student_profile_photo($student_id, string $contents, string $contentType): string
    {
        return $this->attempt(function () use ($student_id, $contents, $contentType) {
            $this->db->ensurePublicStorageBucket(
                'profile-images',
                5 * 1024 * 1024,
                ['image/jpeg', 'image/png', 'image/webp']
            );
            $this->db->uploadStorageObject(
                'profile-images',
                'students/' . (int) $student_id . '/avatar',
                $contents,
                $contentType
            );

            return $this->student_profile_photo_url($student_id);
        }, '');
    }

    public function get_request($request_id, $student_number, $student_id = null): array
    {
        return $this->attempt(function () use ($request_id, $student_number, $student_id) {
            $filters = [
                'request_id' => 'eq.' . (int) $request_id,
                'studentID_no' => 'eq.' . $student_number,
            ];
            if ($student_id !== null) {
                $filters['student_id'] = 'eq.' . (int) $student_id;
            }
            $rows = $this->db->select('tbl_documentrequest', $filters, ['limit' => 1]);
            return $rows[0] ?? [];
        }, []);
    }

    public function get_claim_request($student_id, $document_name, $date_releasing): array
    {
        return $this->attempt(function () use ($student_id, $document_name, $date_releasing) {
            $rows = $this->db->select('tbl_documentrequest', [
                'student_id' => 'eq.' . (int) $student_id,
                'document_name' => 'eq.' . $document_name,
                'date_releasing' => 'eq.' . $date_releasing,
            ], ['limit' => 1]);
            return $rows[0] ?? [];
        }, []);
    }

    public function fetchAll_documentrequest($student_id): array
    {
        return $this->attempt(fn () => $this->db->select(
            'tbl_documentrequest',
            ['student_id' => 'eq.' . (int) $student_id],
            ['order' => 'request_id.desc']
        ), []);
    }

    public function count_numberofstudents(): array
    {
        return $this->attempt(fn () => [['count_students' => $this->countRows('tbl_student')]], [['count_students' => 0]]);
    }

    public function count_numberoftotalrequest(): array
    {
        return $this->attempt(fn () => [['count_request' => $this->countRows('tbl_documentrequest')]], [['count_request' => 0]]);
    }

    public function count_numberoftotalpending($student_id): array
    {
        return $this->attempt(fn () => [['count_pending' => $this->countRows('tbl_documentrequest', [
            'student_id' => 'eq.' . (int) $student_id,
            'status' => 'eq.Pending',
        ])]], [['count_pending' => 0]]);
    }

    public function count_numberoftotalreceived($student_id): array
    {
        return $this->attempt(fn () => [['count_received' => $this->countRows('tbl_documentrequest', [
            'student_id' => 'eq.' . (int) $student_id,
            'status' => 'eq.Received',
        ])]], [['count_received' => 0]]);
    }

    public function change_password($password, $student_id): bool
    {
        return $this->attempt(fn () => $this->db->update(
            'tbl_student',
            ['password' => (string) $password],
            ['student_id' => 'eq.' . (int) $student_id]
        ));
    }

    public function add_request($control_no, $studentID_no, $document_name, $purpose_ofrequesting, $no_ofcopies, $date_request, $received, $student_id): bool
    {
        return $this->attempt(function () use ($control_no, $studentID_no, $document_name, $purpose_ofrequesting, $no_ofcopies, $date_request, $received, $student_id) {
            $this->db->insert('tbl_documentrequest', [
                'control_no' => (string) $control_no,
                'studentID_no' => (string) $studentID_no,
                'document_name' => (string) $document_name,
                'purpose_ofrequesting' => (string) $purpose_ofrequesting,
                'no_ofcopies' => (string) $no_ofcopies,
                'date_request' => (string) $date_request,
                'status' => (string) $received,
                'student_id' => (int) $student_id,
                'notif' => 0,
            ]);
            return true;
        });
    }

    public function add_myrequest($control_no, $studentID_no, $document_name, $date_releasing, $reference_number, $student_id, $status): bool
    {
        return $this->attempt(function () use ($control_no, $studentID_no, $document_name, $date_releasing, $reference_number, $student_id, $status) {
            $this->db->insert('tbl_payment', [
                'control_no' => (string) $control_no,
                'studentID_no' => (string) $studentID_no,
                'document_name' => (string) $document_name,
                'date_releasing' => (string) $date_releasing,
                'reference_number' => (string) $reference_number,
                'student_id' => (int) $student_id,
                'status' => (string) $status,
            ]);
            return true;
        });
    }

    public function edit_request($control_no, $studentID_no, $document_name, $purpose_ofrequesting, $no_ofcopies, $date_request, $request_id, $student_id): bool
    {
        return $this->attempt(fn () => $this->db->update('tbl_documentrequest', [
            'control_no' => (string) $control_no,
            'studentID_no' => (string) $studentID_no,
            'document_name' => (string) $document_name,
            'purpose_ofrequesting' => (string) $purpose_ofrequesting,
            'no_ofcopies' => (string) $no_ofcopies,
            'date_request' => (string) $date_request,
        ], [
            'request_id' => 'eq.' . (int) $request_id,
            'student_id' => 'eq.' . (int) $student_id,
        ]));
    }

    public function delete_request($request_id, $student_id): bool
    {
        return $this->attempt(fn () => $this->db->delete('tbl_documentrequest', [
            'request_id' => 'eq.' . (int) $request_id,
            'student_id' => 'eq.' . (int) $student_id,
        ]));
    }

    public function notification_rows($student_id): array
    {
        return $this->attempt(fn () => $this->db->select('tbl_documentrequest', [
            'student_id' => 'eq.' . (int) $student_id,
            'notif' => 'eq.1',
        ], ['order' => 'request_id.desc', 'limit' => 5]), []);
    }

    public function notification_count($student_id): int
    {
        return $this->attempt(fn () => $this->countRows('tbl_documentrequest', [
            'student_id' => 'eq.' . (int) $student_id,
            'notif' => 'eq.1',
        ]), 0);
    }
}

