-- Optional data import template for existing XAMPP data.
-- Export table data from MySQL/phpMyAdmin, then convert names to this schema:
-- Patients.PatientID -> patients.patient_id
-- Queue.QueueID -> queue.queue_id
-- Visits.VisitID -> visits.visit_id
-- Drugs.DrugID -> drugs.drug_id
-- VisitDrugs.VisitDrugID -> visit_drugs.visit_drug_id
--
-- After inserting explicit IDs, run the sequence fixes below.

select setval(pg_get_serial_sequence('patients', 'patient_id'), coalesce((select max(patient_id) from patients), 1));
select setval(pg_get_serial_sequence('queue', 'queue_id'), coalesce((select max(queue_id) from queue), 1));
select setval(pg_get_serial_sequence('visits', 'visit_id'), coalesce((select max(visit_id) from visits), 1));
select setval(pg_get_serial_sequence('drugs', 'drug_id'), coalesce((select max(drug_id) from drugs), 1));
select setval(pg_get_serial_sequence('visit_drugs', 'visit_drug_id'), coalesce((select max(visit_drug_id) from visit_drugs), 1));
