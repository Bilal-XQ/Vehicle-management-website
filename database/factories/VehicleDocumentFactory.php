<?php

namespace Database\Factories;

use App\Models\VehicleDocument;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleDocument>
 */
class VehicleDocumentFactory extends Factory
{
    protected $model = VehicleDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentType = $this->faker->randomElement(['registration', 'insurance', 'inspection', 'license', 'permit', 'other']);
        $issueDate = $this->faker->dateTimeBetween('-2 years', 'now');
        
        // Calculate expiry date based on document type
        $expiryDate = match($documentType) {
            'registration' => Carbon::parse($issueDate)->addYear(),
            'insurance' => Carbon::parse($issueDate)->addMonths(6),
            'inspection' => Carbon::parse($issueDate)->addMonths(12),
            'license' => Carbon::parse($issueDate)->addYears(5),
            'permit' => Carbon::parse($issueDate)->addMonths(3),
            default => $this->faker->optional(0.8)->dateTimeBetween($issueDate, '+2 years')
        };

        return [
            'vehicle_id' => Vehicle::factory(),
            'document_name' => $this->getDocumentName($documentType),
            'document_type' => $documentType,
            'document_number' => $this->getDocumentNumber($documentType),
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'issuing_authority' => $this->getIssuingAuthority($documentType),
            'notes' => $this->faker->optional(0.4)->sentence(6),
            'file_path' => $this->faker->optional(0.7)->filePath(),
            'file_name' => $this->faker->optional(0.7)->word() . '.pdf',
            'file_size' => $this->faker->optional(0.7)->numberBetween(100000, 5000000), // 100KB to 5MB
            'mime_type' => $this->faker->optional(0.7)->randomElement(['application/pdf', 'image/jpeg', 'image/png']),
        ];
    }

    /**
     * Get document name based on type.
     */
    private function getDocumentName(string $type): string
    {
        return match($type) {
            'registration' => 'Vehicle Registration Certificate',
            'insurance' => 'Vehicle Insurance Policy',
            'inspection' => 'Vehicle Safety Inspection',
            'license' => 'Commercial Driver License',
            'permit' => 'Special Operation Permit',
            default => $this->faker->words(3, true) . ' Document'
        };
    }

    /**
     * Get document number based on type.
     */
    private function getDocumentNumber(string $type): string
    {
        return match($type) {
            'registration' => 'REG-' . $this->faker->bothify('??#####'),
            'insurance' => 'INS-' . $this->faker->bothify('###-??-####'),
            'inspection' => 'INS-' . $this->faker->bothify('#######'),
            'license' => 'DL-' . $this->faker->bothify('?########'),
            'permit' => 'PER-' . $this->faker->bothify('####??'),
            default => $this->faker->bothify('DOC-#######')
        };
    }

    /**
     * Get issuing authority based on type.
     */
    private function getIssuingAuthority(string $type): string
    {
        return match($type) {
            'registration' => 'Department of Motor Vehicles',
            'insurance' => $this->faker->randomElement(['State Farm', 'Geico', 'Progressive', 'Allstate', 'Liberty Mutual']),
            'inspection' => 'State Vehicle Inspection Bureau',
            'license' => 'Department of Transportation',
            'permit' => 'Special Permits Office',
            default => $this->faker->company() . ' Authority'
        };
    }

    /**
     * Indicate that the document is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiry_date' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }

    /**
     * Indicate that the document is expiring soon.
     */
    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiry_date' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * Indicate that the document is valid for a long time.
     */
    public function longTermValid(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiry_date' => $this->faker->dateTimeBetween('+6 months', '+5 years'),
        ]);
    }

    /**
     * Indicate that the document has a file attached.
     */
    public function withFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_path' => 'vehicle-documents/' . $this->faker->uuid() . '.pdf',
            'file_name' => $this->faker->word() . '_document.pdf',
            'file_size' => $this->faker->numberBetween(500000, 3000000),
            'mime_type' => 'application/pdf',
        ]);
    }

    /**
     * Indicate that the document has no file attached.
     */
    public function withoutFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
        ]);
    }

    /**
     * Create a registration document.
     */
    public function registration(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'registration',
            'document_name' => 'Vehicle Registration Certificate',
            'document_number' => 'REG-' . $this->faker->bothify('??#####'),
            'issuing_authority' => 'Department of Motor Vehicles',
            'expiry_date' => Carbon::parse($attributes['issue_date'])->addYear(),
        ]);
    }

    /**
     * Create an insurance document.
     */
    public function insurance(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'insurance',
            'document_name' => 'Vehicle Insurance Policy',
            'document_number' => 'INS-' . $this->faker->bothify('###-??-####'),
            'issuing_authority' => $this->faker->randomElement(['State Farm', 'Geico', 'Progressive', 'Allstate']),
            'expiry_date' => Carbon::parse($attributes['issue_date'])->addMonths(6),
        ]);
    }

    /**
     * Create an inspection document.
     */
    public function inspection(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'inspection',
            'document_name' => 'Vehicle Safety Inspection',
            'document_number' => 'INS-' . $this->faker->bothify('#######'),
            'issuing_authority' => 'State Vehicle Inspection Bureau',
            'expiry_date' => Carbon::parse($attributes['issue_date'])->addYear(),
        ]);
    }
}
