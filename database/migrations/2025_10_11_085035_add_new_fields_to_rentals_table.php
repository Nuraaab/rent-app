<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            // Add new fields for the updated Add Property wizard
            $table->string('property_type')->nullable()->after('category_id'); // House, Apartment, Condo, etc.
            $table->enum('listing_type', ['rent', 'sale'])->default('rent')->after('property_type'); // Rent or Sale
            $table->string('sqft')->nullable()->after('number_of_baths'); // Square feet
            $table->string('country')->nullable()->after('address'); // Country
            $table->string('street_address')->nullable()->after('country'); // Street address
            $table->string('apt')->nullable()->after('street_address'); // Apt, suite, etc.
            $table->string('city')->nullable()->after('apt'); // City
            $table->string('state')->nullable()->after('city'); // State/Province
            $table->string('zip_code')->nullable()->after('state'); // ZIP/Postal code
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            // Drop the columns in reverse order
            $table->dropColumn([
                'property_type',
                'listing_type',
                'sqft',
                'country',
                'street_address',
                'apt',
                'city',
                'state',
                'zip_code'
            ]);
        });
    }
};
