use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('opd_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_id')->constrained('opds')->onDelete('cascade');
            $table->string('alamat_email');
            $table->string('keterangan_pic')->nullable(); // cth: 'Hardianto (Admin Jaringan)'
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('opd_emails');
    }
};