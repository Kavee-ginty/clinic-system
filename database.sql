CREATE DATABASE IF NOT EXISTS clinic_system;
USE clinic_system;

CREATE TABLE IF NOT EXISTS Patients (
    PatientID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    DOB DATE NOT NULL,
    Gender VARCHAR(10) NOT NULL,
    Phone VARCHAR(20) NOT NULL,
    Address TEXT,
    RegisteredDate DATE DEFAULT CURRENT_DATE,
    INDEX (LastName),
    INDEX (Phone)
);

CREATE TABLE IF NOT EXISTS Queue (
    QueueID INT AUTO_INCREMENT PRIMARY KEY,
    PatientID INT NOT NULL,
    QueueNumber INT NOT NULL,
    QueueDate DATE NOT NULL,
    Status ENUM('waiting','with_doctor','completed') DEFAULT 'waiting',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (PatientID) REFERENCES Patients(PatientID),
    INDEX (QueueDate),
    INDEX (Status)
);

CREATE TABLE IF NOT EXISTS Visits (
    VisitID INT AUTO_INCREMENT PRIMARY KEY,
    PatientID INT NOT NULL,
    QueueID INT NOT NULL,
    VisitDateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Complaint TEXT,
    Examination TEXT,
    Investigation TEXT,
    Diagnosis TEXT,
    Treatment TEXT,
    Referals TEXT,
    Notes TEXT,
    FOREIGN KEY (PatientID) REFERENCES Patients(PatientID),
    FOREIGN KEY (QueueID) REFERENCES Queue(QueueID),
    INDEX (PatientID)
);
