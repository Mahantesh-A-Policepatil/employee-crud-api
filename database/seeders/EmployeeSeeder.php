<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $departments = collect([
            ['name' => 'Engineering', 'description' => 'Product and software development'],
            ['name' => 'Human Resources', 'description' => 'People operations and recruitment'],
            ['name' => 'Quality Assurance', 'description' => 'Testing and release quality'],
            ['name' => 'Operations', 'description' => 'Business operations and support'],
        ])->mapWithKeys(function ($department) {
            $model = Department::firstOrCreate(
                ['name' => $department['name']],
                ['description' => $department['description']]
            );

            return [$model->name => $model];
        });

        $projects = Project::pluck('id', 'name');

        $employees = [
            ['Aarav Sharma','aarav.sharma@example.com','9876500001','Senior Software Engineer','Engineering','Employee Management System','PHP, Laravel, MySQL, Git','1992-03-15','2018-06-11','aarav.jpg',8.0],
            ['Meera Iyer','meera.iyer@example.com','9876500002','Frontend Developer','Engineering','Hotel Management System','ReactJS, JavaScript, HTML, CSS','1995-08-21','2020-02-10','meera.jpg',5.5],
            ['Rohan Desai','rohan.desai@example.com','9876500003','Backend Developer','Engineering','Hospital Management System','PHP, Laravel, REST API, MySQL','1993-12-09','2019-04-15','rohan.jpg',6.5],
            ['Ananya Rao','ananya.rao@example.com','9876500004','DevOps Engineer','Engineering','Air Ticket Reservation System','Docker, Kubernetes, AWS, Jenkins','1994-05-18','2021-01-18','ananya.jpg',4.5],
            ['Vikram Singh','vikram.singh@example.com','9876500005','Engineering Manager','Engineering','Employee Management System','Leadership, Agile, Scrum, System Design','1988-01-30','2015-09-01','vikram.jpg',12.0],

            ['Priya Nair','priya.nair@example.com','9876500006','HR Manager','Human Resources','Hotel Management System','Recruitment, Payroll, Employee Relations','1990-04-14','2017-08-07','priya.jpg',9.0],
            ['Neha Kapoor','neha.kapoor@example.com','9876500007','Talent Acquisition Specialist','Human Resources','Hospital Management System','Hiring, Interviewing, LinkedIn Recruiting','1994-09-05','2021-03-08','neha.jpg',4.0],
            ['Karan Patel','karan.patel@example.com','9876500008','People Operations Executive','Human Resources','Air Ticket Reservation System','HRMS, Onboarding, Documentation','1996-11-12','2022-07-18','karan.jpg',3.0],

            ['Ishita Mehta','ishita.mehta@example.com','9876500009','QA Lead','Quality Assurance','Employee Management System','Manual Testing, Selenium, Test Planning','1991-06-24','2017-10-16','ishita.jpg',8.5],
            ['Arjun Verma','arjun.verma@example.com','9876500010','Automation Test Engineer','Quality Assurance','Hotel Management System','Selenium, Cypress, Java','1993-02-11','2019-11-25','arjun.jpg',6.0],
            ['Sneha Kulkarni','sneha.kulkarni@example.com','9876500011','Quality Analyst','Quality Assurance','Hospital Management System','Regression Testing, JIRA','1995-07-29','2020-08-03','sneha.jpg',5.0],
            ['Rahul Joshi','rahul.joshi@example.com','9876500012','Performance Test Engineer','Quality Assurance','Air Ticket Reservation System','JMeter, LoadRunner, Performance Testing','1992-10-17','2018-01-22','rahul.jpg',7.5],

            ['Kavya Reddy','kavya.reddy@example.com','9876500013','Operations Manager','Operations','Employee Management System','Operations Management, Reporting','1989-03-08','2016-05-09','kavya.jpg',10.5],
            ['Aditya Menon','aditya.menon@example.com','9876500014','Business Operations Analyst','Operations','Hotel Management System','Excel, Power BI, Data Analysis','1994-12-01','2020-10-12','aditya.jpg',5.0],
            ['Pooja Shah','pooja.shah@example.com','9876500015','Customer Support Lead','Operations','Hospital Management System','Customer Support, CRM, Communication','1993-09-20','2019-06-24','pooja.jpg',6.0],
            ['Nikhil Bhat','nikhil.bhat@example.com','9876500016','Systems Administrator','Operations','Air Ticket Reservation System','Linux, Windows Server, Networking','1991-01-13','2017-02-20','nikhil.jpg',9.5],
        ];

        collect($employees)->each(function ($employee) use ($departments, $projects) {

            [
                $name,
                $email,
                $phone,
                $designation,
                $department,
                $project,
                $skills,
                $dateOfBirth,
                $dateOfJoining,
                $profilePic,
                $experience
            ] = $employee;

            Employee::updateOrCreate(
                ['email' => $email],
                [
                    'department_id' => $departments[$department]->id,
                    'project_id' => $projects[$project] ?? null,
                    'name' => $name,
                    'phone' => $phone,
                    'designation' => $designation,
                    'skills' => $skills,
                    'date_of_birth' => $dateOfBirth,
                    'date_of_joining' => $dateOfJoining,
                    'profile_pic' => $profilePic,
                    'total_years_of_experience' => $experience,
                ]
            );
        });
    }
}
