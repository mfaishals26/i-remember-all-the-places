<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * This migration is intentionally safe when memory_photos already
         * exists. That can happen if the table was created by an earlier
         * attempt while the migration itself was not recorded.
         */
        if (!Schema::hasTable('memory_photos')) {
            Schema::create('memory_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('memory_id')
                    ->constrained('memories')
                    ->cascadeOnDelete();
                $table->string('photo');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('memory_photos', 'memory_id')) {
                Schema::table('memory_photos', function (Blueprint $table) {
                    $table->foreignId('memory_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('memories')
                        ->cascadeOnDelete();
                });
            }

            if (!Schema::hasColumn('memory_photos', 'photo')) {
                Schema::table('memory_photos', function (Blueprint $table) {
                    $table->string('photo')->nullable()->after('memory_id');
                });
            }

            if (!Schema::hasColumn('memory_photos', 'sort_order')) {
                Schema::table('memory_photos', function (Blueprint $table) {
                    $table->unsignedInteger('sort_order')->default(0)->after('photo');
                });
            }

            if (!Schema::hasColumn('memory_photos', 'created_at')) {
                Schema::table('memory_photos', function (Blueprint $table) {
                    $table->timestamps();
                });
            }
        }

        /* Migrate legacy memories.photo into memory_photos without duplicates. */
        if (Schema::hasColumn('memories', 'photo')) {
            DB::table('memories')
                ->whereNotNull('photo')
                ->where('photo', '!=', '')
                ->orderBy('id')
                ->get()
                ->each(function ($memory) {
                    $alreadyExists = DB::table('memory_photos')
                        ->where('memory_id', $memory->id)
                        ->where('photo', $memory->photo)
                        ->exists();

                    if (!$alreadyExists) {
                        DB::table('memory_photos')->insert([
                            'memory_id' => $memory->id,
                            'photo' => $memory->photo,
                            'sort_order' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('memory_photos');
    }
};
