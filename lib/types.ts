export type Role = "admin" | "doctor" | "receptionist";

export type JsonRecord = Record<string, unknown>;

export type Patient = {
  patient_id: number;
  patient_number: string | null;
  first_name: string;
  last_name: string;
  dob: string;
  gender: string;
  phone: string;
  address: string | null;
  nic: string | null;
  age: number | null;
  registered_date: string;
  visit_count?: number;
};

export type Drug = {
  drug_id: number;
  drug_name: string;
  dose: string | null;
  batch_number: string | null;
  quantity: number;
  unit_price: number;
};

export type QueueRow = {
  queue_id: number;
  queue_number: number;
  status: "waiting" | "with_doctor" | "completed";
  patient_id: number;
  first_name: string;
  last_name: string;
  gender: string;
  dob: string;
  previous_visits: number;
  visit_id: number | null;
  total_bill: number | null;
  visit_fee: number | null;
  is_paid: boolean;
};
