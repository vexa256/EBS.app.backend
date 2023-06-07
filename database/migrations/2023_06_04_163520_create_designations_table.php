<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('Designation')->unique();
            $table->string('DesignationID')->unique();
            $table->timestamps();
        });

        $designations = [
            // Put all the titles you provided here...
            "Community Health Volunteer",
            "Community Health Worker",
            "District IDSR Focal Person",
            "Health Center Manager",
            "Community Health Nurse",
            "Health Center Infection Control Officer",
            "Health Center Surveillance Focal Point",
            "Health Center Laboratory Technician",
            "Pharmacy Coordinator",
            "Nutrition Services Coordinator",
            "Mental Health Services Coordinator",
            "Maternal and Child Health Coordinator",
            "Dental Services Coordinator",
            "Reproductive Health Services Coordinator",
            "Chronic Disease Management Coordinator",
            "Immunization Services Coordinator",
            "Health Promotion and Education Officer",
            "Health Center Records Officer",
            "Health Center Maintenance Supervisor",
            "Health Center IT Support Officer",
            "Emergency Services Coordinator",
            "Outpatient Services Coordinator",
            "Social Work Services Coordinator",
            "Radiology Technician",
            "Physical Therapy Services Coordinator",
            "Occupational Health Services Coordinator",
            "Environmental Health Services Coordinator",
            "Health Center Finance Officer", "District Medical Superintendent",
            "District Patient Services Manager",
            "District Health Safety and Environment Officer",
            "Provincial Medical Quality Assurance Manager",
            "Provincial Hospital Human Resources Manager",
            "Provincial Hospital Information Technology Manager",
            "Provincial Patient Safety Officer",
            "National Hospital Pharmacy Services Director",
            "National Hospital Financial Services Director",
            "National Director of Pediatric Services",
            "National Director of Surgical Services",
            "National Director of Diagnostic Imaging",
            "National Director of Rehabilitation Services",
            "National Director of Mental Health Services",
            "National Director of Maternity Services",
            "National Director of Emergency Services",
            "National Director of Palliative Care Services",
            "National Director of Intensive Care Services",
            "National Director of Orthopedic Services",
            "National Director of Oncology Services",
            "National Director of Cardiovascular Services",
            "National Director of Neurology Services",
            "National Director of Geriatric Services",
            "District Hospital Administrator",
            "District Infection Control Coordinator",
            "District Surveillance Officer",
            "District Hospital Matron",
            "District Hospital Chief Medical Officer",
            "District Laboratory Services Manager",
            "Provincial Hospital Director",
            "Provincial Chief Nursing Officer",
            "Provincial Clinical Services Director",
            "Provincial Health Information Manager",
            "Provincial Quality Assurance Coordinator",
            "National Hospital CEO",
            "National Director of Hospital Services",
            "National Director of Clinical Services",
            "National Director of Nursing Services",
            "National Infection Prevention and Control Manager",
            "National Hospital Surveillance Coordinator",
            "National Hospital Quality Improvement Officer",
            "National Health Information Systems Director",
            "National Laboratory Services Director", "Community Health Volunteer",
            "Community Health Worker",
            "District IDSR Focal Person",
            "Provincial EBS Coordinator",
            "Regional Epidemic Preparedness Officer",
            "National Outbreak Response Manager",
            "Village Health Committee Member",
            "Zonal Surveillance Officer",
            "County Health Director",
            "Hospital Infection Prevention and Control Officer",
            "Mobile Health Clinic Coordinator",
            "Refugee Camp Health Supervisor",
            "District Health Promotion Officer",
            "Provincial IDSR Trainer",
            "School Health Coordinator",
            "Public Health Laboratory Scientist",
            "Regional Vector Control Specialist",
            "Animal Health Technician",
            "One Health Focal Point",
            "Data Management Specialist",
            "Health Information Systems Officer",
            "Field Epidemiologist",
            "Malaria Surveillance Officer",
            "Tuberculosis Case Detection Officer",
            "HIV/AIDS Surveillance Coordinator",
            "Vaccine Preventable Disease Surveillance Officer",
            "National Antimicrobial Resistance Surveillance Focal Person",
            "Regional Neglected Tropical Disease Coordinator",
            "Maternal and Child Health Surveillance Officer",
            "Climate Change and Health Surveillance Specialist"
        ];

        foreach ($designations as $designation) {
            // Check if the designation is already in the database
            if (DB::table('designations')->where('Designation', $designation)->doesntExist()) {
                DB::table('designations')->insert([
                    'Designation' => $designation,
                    'DesignationID' => rand(100000, 999999) . uniqid()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
