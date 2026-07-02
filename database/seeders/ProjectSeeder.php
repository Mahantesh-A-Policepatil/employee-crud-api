<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        $projects = [
            [
                'name' => 'Employee Management System',
                'description' => 'Manages employee records, departments, roles, and permissions.',
            ],
            [
                'name' => 'Hotel Management System',
                'description' => 'Manages hotel reservations, guests, rooms, and billing.',
            ],
            [
                'name' => 'Hospital Management System',
                'description' => 'Manages patients, appointments, clinical staff, and hospital operations.',
            ],
            [
                'name' => 'Air Ticket Reservation System',
                'description' => 'Manages flights, passenger bookings, tickets, and reservations.',
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['name' => $project['name']],
                ['description' => $project['description']]
            );
        }
    }
}
