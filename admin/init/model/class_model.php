<?php

require_once dirname(__DIR__, 3) . '/init/model/bootstrap.php';

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

    private function one(string $table, array $filters): array
    {
        return $this->attempt(function () use ($table, $filters) {
            $rows = $this->db->select($table, $filters, ['limit' => 1]);
            return $rows[0] ?? [];
        }, []);
    }

    private function insert(string $table, array $values): bool
    {
        return $this->attempt(function () use ($table, $values) {
            $this->db->insert($table, $values);
            return true;
        });
    }

    private function update(string $table, array $values, array $filters): bool
    {
        return $this->attempt(fn () => $this->db->update($table, $values, $filters));
    }

    private function remove(string $table, array $filters): bool
    {
        return $this->attempt(fn () => $this->db->delete($table, $filters));
    }

    private function countResult(string $alias, string $table, array $filters = []): array
    {
        return $this->attempt(fn () => [[$alias => $this->countRows($table, $filters)]], [[$alias => 0]]);
    }

    public function login($username, $password, $status): array
    {
        return $this->attempt(function () use ($username, $password, $status) {
            $rows = $this->db->select('tbl_usermanagement', [
                'username' => 'eq.' . $username,
                'status' => 'eq.' . $status,
            ], ['limit' => 1]);
            $user = $rows[0] ?? null;
            $valid = is_array($user) && $this->passwordMatches((string) $password, (string) $user['password']);
            return ['user_id' => $valid ? (int) $user['user_id'] : 0, 'count' => $valid ? 1 : 0];
        }, ['user_id' => 0, 'count' => 0]);
    }

    public function user_account($user_id): array
    {
        $user = $this->get_user($user_id);
        return ['complete_name' => $user['complete_name'] ?? ''];
    }

    public function get_request($request_id, $student_number): array
    {
        return $this->one('tbl_documentrequest', [
            'request_id' => 'eq.' . (int) $request_id,
            'studentID_no' => 'eq.' . $student_number,
        ]);
    }

    public function get_strand($strand_id, $strand_name): array
    {
        return $this->one('tbl_strand', [
            'strand_id' => 'eq.' . (int) $strand_id,
            'strand_name' => 'eq.' . $strand_name,
        ]);
    }

    public function get_student($student_id, $student_number): array
    {
        return $this->one('tbl_student', [
            'student_id' => 'eq.' . (int) $student_id,
            'studentID_no' => 'eq.' . $student_number,
        ]);
    }

    public function get_user($user_id, ?string $complete_name = null): array
    {
        $filters = ['user_id' => 'eq.' . (int) $user_id];
        if ($complete_name !== null) {
            $filters['complete_name'] = 'eq.' . $complete_name;
        }
        return $this->one('tbl_usermanagement', $filters);
    }

    public function fetchAll_strand(): array
    {
        return $this->attempt(fn () => $this->db->select('tbl_strand', [], ['order' => 'strand_id.asc']), []);
    }

    public function add_strand($strand_name, $strand_description): bool
    {
        return $this->insert('tbl_strand', [
            'strand_name' => (string) $strand_name,
            'strand_description' => (string) $strand_description,
        ]);
    }

    public function edit_strand($strand_name, $strand_description, $strand_id): bool
    {
        return $this->update('tbl_strand', [
            'strand_name' => (string) $strand_name,
            'strand_description' => (string) $strand_description,
        ], ['strand_id' => 'eq.' . (int) $strand_id]);
    }

    public function delete_strand($strand_id): bool
    {
        return $this->remove('tbl_strand', ['strand_id' => 'eq.' . (int) $strand_id]);
    }

    public function add_student($IDNumber, $first_name, $middle_name, $last_name, $strand, $grade_level, $date_ofbirth, $gender, $complete_address, $email_address, $mobile_number, $username, $password, $status): bool
    {
        return $this->insert('tbl_student', [
            'studentID_no' => (string) $IDNumber,
            'first_name' => (string) $first_name,
            'middle_name' => (string) $middle_name,
            'last_name' => (string) $last_name,
            'strand' => (string) $strand,
            'grade_level' => (string) $grade_level,
            'date_ofbirth' => (string) $date_ofbirth,
            'gender' => (string) $gender,
            'complete_address' => (string) $complete_address,
            'email_address' => (string) $email_address,
            'mobile_number' => (string) $mobile_number,
            'username' => (string) $username,
            'password' => (string) $password,
            'account_status' => (string) $status,
        ]);
    }

    public function fetchAll_student(): array
    {
        return $this->attempt(fn () => $this->db->select('tbl_student', [], ['order' => 'student_id.desc']), []);
    }

    public function edit_student($first_name, $middle_name, $last_name, $strand, $grade_level, $date_ofbirth, $gender, $complete_address, $email_address, $mobile_number, $username, $password, $account_status, $student_id): bool
    {
        return $this->update('tbl_student', [
            'first_name' => (string) $first_name,
            'middle_name' => (string) $middle_name,
            'last_name' => (string) $last_name,
            'strand' => (string) $strand,
            'grade_level' => (string) $grade_level,
            'date_ofbirth' => (string) $date_ofbirth,
            'gender' => (string) $gender,
            'complete_address' => (string) $complete_address,
            'email_address' => (string) $email_address,
            'mobile_number' => (string) $mobile_number,
            'username' => (string) $username,
            'password' => (string) $password,
            'account_status' => (string) $account_status,
        ], ['student_id' => 'eq.' . (int) $student_id]);
    }

    public function delete_student($student_id): bool
    {
        return $this->remove('tbl_student', ['student_id' => 'eq.' . (int) $student_id]);
    }

    public function fetchAll_document(): array
    {
        return [];
    }

    public function delete_document($document_id): bool
    {
        return false;
    }

    public function fetchAll_documentrequest(): array
    {
        return $this->attempt(fn () => $this->db->select('tbl_documentrequest', [], ['order' => 'request_id.desc']), []);
    }

    public function edit_request($control_no, $studentID_no, $document_name, $purpose_ofrequesting, $no_ofcopies, $date_request, $date_releasing, $processing_officer, $status, $request_id): bool
    {
        return $this->update('tbl_documentrequest', [
            'control_no' => (string) $control_no,
            'studentID_no' => (string) $studentID_no,
            'document_name' => (string) $document_name,
            'purpose_ofrequesting' => (string) $purpose_ofrequesting,
            'no_ofcopies' => (string) $no_ofcopies,
            'date_request' => (string) $date_request,
            'date_releasing' => (string) $date_releasing,
            'processing_officer' => (string) $processing_officer,
            'status' => (string) $status,
            'notif' => 1,
        ], ['request_id' => 'eq.' . (int) $request_id]);
    }

    public function delete_request($request_id): bool
    {
        return $this->remove('tbl_documentrequest', ['request_id' => 'eq.' . (int) $request_id]);
    }

    public function fetchAll_payment(): array
    {
        return $this->attempt(fn () => $this->db->select('tbl_payment', [], ['order' => 'payment_id.desc']), []);
    }

    public function edit_payment($control_no, $total_amount, $amount_paid, $date_ofpayment, $proof_ofpayment, $status, $payment_id): bool
    {
        return $this->update('tbl_payment', [
            'control_no' => (string) $control_no,
            'total_amount' => (float) $total_amount,
            'amount_paid' => (float) $amount_paid,
            'date_ofpayment' => (string) $date_ofpayment,
            'proof_ofpayment' => (string) $proof_ofpayment,
            'status' => (string) $status,
        ], ['payment_id' => 'eq.' . (int) $payment_id]);
    }

    public function delete_payment($payment_id): bool
    {
        return $this->remove('tbl_payment', ['payment_id' => 'eq.' . (int) $payment_id]);
    }

    public function add_user($complete_name, $designation, $email_address, $phone_number, $username, $password, $status): bool
    {
        return $this->insert('tbl_usermanagement', [
            'complete_name' => (string) $complete_name,
            'designation' => (string) $designation,
            'email_address' => (string) $email_address,
            'phone_number' => (string) $phone_number,
            'username' => (string) $username,
            'password' => (string) $password,
            'status' => (string) $status,
        ]);
    }

    public function fetchAll_user(): array
    {
        return $this->attempt(fn () => $this->db->select('tbl_usermanagement', [], ['order' => 'user_id.desc']), []);
    }

    public function edit_user($complete_name, $designation, $email_address, $phone_number, $username, $password, $status, $user_id): bool
    {
        return $this->update('tbl_usermanagement', [
            'complete_name' => (string) $complete_name,
            'designation' => (string) $designation,
            'email_address' => (string) $email_address,
            'phone_number' => (string) $phone_number,
            'username' => (string) $username,
            'password' => (string) $password,
            'status' => (string) $status,
        ], ['user_id' => 'eq.' . (int) $user_id]);
    }

    public function delete_user($user_id): bool
    {
        return $this->remove('tbl_usermanagement', ['user_id' => 'eq.' . (int) $user_id]);
    }

    public function count_numberofstudents(): array
    {
        return $this->countResult('count_students', 'tbl_student');
    }

    public function count_numberoftotalrequest(): array
    {
        return $this->countResult('count_request', 'tbl_documentrequest');
    }

    public function count_numberoftotalpending(): array
    {
        return $this->countResult('count_pending', 'tbl_documentrequest', ['status' => 'eq.Pending']);
    }

    public function count_numberoftotalpaid(): array
    {
        return $this->countResult('count_paid', 'tbl_documentrequest', ['status' => 'eq.Paid']);
    }

    public function count_numberoftotalreceived(): array
    {
        return $this->countResult('count_received', 'tbl_documentrequest', ['status' => 'eq.Received']);
    }

    public function count_groupbymonth(): array
    {
        return [];
    }

    public function count_groupbystrand(): array
    {
        return $this->attempt(function () {
            $rows = $this->db->select('tbl_student', [], ['select' => 'strand']);
            $counts = [];
            foreach ($rows as $row) {
                $strand = (string) ($row['strand'] ?? '');
                $counts[$strand] = ($counts[$strand] ?? 0) + 1;
            }
            $result = [];
            foreach ($counts as $strand => $count) {
                $result[] = ['count_strandname' => $count, 'strand' => $strand];
            }
            return $result;
        }, []);
    }

    public function latest_notifications(): array
    {
        return $this->attempt(fn () => $this->db->select('tbl_documentrequest', [], [
            'order' => 'request_id.desc',
            'limit' => 5,
        ]), []);
    }

    public function unseen_notification_count(): int
    {
        return $this->attempt(fn () => $this->countRows('tbl_documentrequest', ['notif' => 'eq.0']), 0);
    }

    public function mark_notifications_seen(): bool
    {
        return $this->update('tbl_documentrequest', ['notif' => 1], ['notif' => 'eq.0']);
    }
}

