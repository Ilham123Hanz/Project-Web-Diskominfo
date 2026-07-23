use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('opd_aplikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_id')->constrained('opds')->onDelete('cascade');
            $table->string('nama_sistem'); // cth: 'SIP-O-SIBER Monitoring'
            $table->string('kode_aset')->nullable(); // cth: 'ASSET-2026-XXXX'
            $table->string('domain_url'); // cth: 'https://domain.jatimprov.go.id'
            $table->enum('status_operasional', ['aktif', 'non-aktif', 'pengembangan'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('opd_aplikasis');
    }
};