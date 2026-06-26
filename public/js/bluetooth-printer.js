/**
 * bluetooth-printer.js
 * Koneksi ke thermal printer Bluetooth Low Energy (BLE) dan cetak struk
 * pakai komando ESC/POS, langsung dari browser (tanpa app native).
 *
 * SYARAT:
 * - Browser HARUS Chrome / Edge di Android (Web Bluetooth API tidak ada di Safari/iOS).
 * - Printer HARUS Bluetooth Low Energy (BLE), bukan Bluetooth Classic/SPP.
 * - Halaman HARUS diakses lewat HTTPS (atau localhost saat development).
 *   Karena nanti di taruh di VPS, pastikan domainnya pakai SSL (https://),
 *   Web Bluetooth API menolak jalan di halaman http:// biasa.
 *
 * Library encoder: @point-of-sale/receipt-printer-encoder
 * (penerus esc-pos-encoder, support format ESC/POS & StarPRNT)
 */

// Beberapa printer thermal BLE umumnya mengekspos salah satu dari UUID generik ini
// untuk "Serial Port Profile over BLE". Karena tiap merk printer (Goojprt, Xprinter,
// EPPOS, dll) kadang beda, kita pakai filter yang menerima SEMUA device dulu
// (acceptAllDevices) lalu nanti baru dicoba beberapa service UUID yang umum.
// Kalau printer Anda punya UUID spesifik, sebaiknya cari di manual/datasheet-nya
// dan masukkan ke daftar SERVICE_UUIDS supaya proses scan lebih cepat & akurat.

const SERVICE_UUIDS_CANDIDATES = [
    '000018f0-0000-1000-8000-00805f9b34fb', // umum dipakai printer thermal generic BLE
    '0000ffe0-0000-1000-8000-00805f9b34fb', // varian lain yang sering dipakai (HM-10 style module)
];

class BluetoothPrinter {
    constructor() {
        this.device = null;
        this.characteristic = null;
    }

    /**
     * Memunculkan dialog pilih device Bluetooth dari browser.
     * HARUS dipanggil dari dalam event handler klik user (tidak bisa otomatis),
     * ini pembatasan keamanan dari browser, bukan dari kode kita.
     */
    async connect() {
        if (!navigator.bluetooth) {
            throw new Error(
                'Web Bluetooth tidak didukung di browser ini. ' +
                'Gunakan Chrome atau Edge di Android.'
            );
        }

        // Minta user pilih device. acceptAllDevices:true supaya printer apapun
        // (meski UUID service-nya tidak kita tahu) tetap muncul di list.
        this.device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: SERVICE_UUIDS_CANDIDATES,
        });

        const server = await this.device.gatt.connect();

        // Cari service & characteristic yang bisa ditulis (write)
        let foundCharacteristic = null;
        for (const uuid of SERVICE_UUIDS_CANDIDATES) {
            try {
                const service = await server.getPrimaryService(uuid);
                const characteristics = await service.getCharacteristics();
                foundCharacteristic = characteristics.find(
                    (c) => c.properties.write || c.properties.writeWithoutResponse
                );
                if (foundCharacteristic) break;
            } catch (e) {
                // service ini tidak ada di printer ini, coba kandidat berikutnya
                continue;
            }
        }

        if (!foundCharacteristic) {
            // Fallback: ambil semua service yang tersedia dan cari characteristic writable
            const services = await server.getPrimaryServices();
            for (const service of services) {
                const characteristics = await service.getCharacteristics();
                foundCharacteristic = characteristics.find(
                    (c) => c.properties.write || c.properties.writeWithoutResponse
                );
                if (foundCharacteristic) break;
            }
        }

        if (!foundCharacteristic) {
            throw new Error(
                'Tidak menemukan characteristic yang bisa ditulis di printer ini. ' +
                'Kemungkinan printer ini Bluetooth Classic (SPP), bukan BLE — ' +
                'Web Bluetooth API tidak bisa dipakai untuk tipe ini.'
            );
        }

        this.characteristic = foundCharacteristic;
        return true;
    }

    isConnected() {
        return !!(this.device && this.device.gatt.connected);
    }

    /**
     * Kirim bytes (Uint8Array) ke printer.
     * Dipecah per 100-180 byte karena banyak printer BLE punya batas ukuran
     * MTU/paket per write — kirim sekaligus dalam jumlah besar sering gagal diam-diam.
     */
    async print(bytes) {
        if (!this.isConnected()) {
            throw new Error('Printer belum terkoneksi. Panggil connect() dulu.');
        }

        const CHUNK_SIZE = 100;
        for (let i = 0; i < bytes.length; i += CHUNK_SIZE) {
            const chunk = bytes.slice(i, i + CHUNK_SIZE);
            if (this.characteristic.properties.writeWithoutResponse) {
                await this.characteristic.writeValueWithoutResponse(chunk);
            } else {
                await this.characteristic.writeValue(chunk);
            }
            // delay kecil supaya buffer printer tidak overflow
            await new Promise((resolve) => setTimeout(resolve, 30));
        }
    }

    disconnect() {
        if (this.device && this.device.gatt.connected) {
            this.device.gatt.disconnect();
        }
    }
}

// Instance tunggal supaya tidak perlu connect ulang setiap cetak
// (selama browser/tab tidak ditutup, koneksi bisa dipakai berulang)
const printerConnection = new BluetoothPrinter();

/**
 * Ambil data transaksi dari endpoint CI4, encode jadi ESC/POS, lalu cetak.
 * @param {number} transaksiId
 */
async function cetakStruk(transaksiId) {
    const statusEl = document.getElementById('printStatus');
    const setStatus = (msg) => {
        if (statusEl) statusEl.textContent = msg;
        console.log('[Print]', msg);
    };

    try {
        // 1. Ambil data dari CI4
        setStatus('Mengambil data transaksi...');
        const res = await fetch('transaction/struk/' + transaksiId);
        const data = await res.json();

        console.log(data)
        if (!data.success) {
            setStatus('Gagal: ' + data.message);
            return;
        }

        // 2. Connect ke printer (kalau belum konek)
        if (!printerConnection.isConnected()) {
            setStatus('Membuka pilihan Bluetooth, pilih printer Anda...');
            await printerConnection.connect();
        }

        // 3. Encode struk jadi bytes ESC/POS
        setStatus('Menyiapkan struk...');
        // 1. Fungsi bantuan untuk membuat teks rata kiri-kanan secara manual
        // 1. Tambahkan margin 2 spasi di dalam fungsi bantuan ini
        // 1. Fungsi kolom disesuaikan untuk kertas 80mm (totalKolom = 42)
        // 1. Fungsi kolom dimaksimalkan untuk kapasitas penuh kertas 80mm (totalKolom = 48)
        // 1. Inisialisasi printer dengan mendefinisikan total kolom kertas 80mm secara eksplisit
        let encoder = new ReceiptPrinterEncoder({
            columns: 48 // Kita set kapasitas kertas ke 48 karakter agar area cetak maksimal
        });

        // 2. Fungsi hitung spasi dinamis agar teks KANAN dijamin lurus sejajar di ujung kertas
        function buatBarisRapi(kiri, kanan, totalKolom = 48) {
            let marginLeft = "    "; // Margin kiri tetap 4 spasi biar tidak mepet

            // Hitung berapa panjang maksimal teks yang bisa ditampung di antara margin
            let areaTeks = totalKolom - marginLeft.length;

            // Hitung sisa spasi tengah secara dinamis
            let sisaSpasi = areaTeks - (kiri.length + kanan.length);

            if (sisaSpasi > 0) {
                return marginLeft + kiri + ' '.repeat(sisaSpasi) + kanan;
            }
            return marginLeft + kiri + ' ' + kanan;
        }

        // 3. Mulai Menyusun Struk
        let builder = encoder
            .initialize()
            .codepage('cp858')
            .align('center')
            .bold(true)
            .line(data.toko.nama)
            .bold(false)
            .line(data.toko.alamat)
            .newline()
            .align('left')
            .line(`    Tgl: ${data.tanggal}`) // Margin kiri 4 spasi
            .line('    --------------------------------------------'); // Garis pembatas (total 48 kolom)

        // 4. Loop Item Produk
        data.items.forEach((item) => {
            // Nama produk (Baris 1)
            builder = builder.line(`    ${item.nama}`);

            // Detail Qty & Subtotal (Baris 2) -> Diproses lewat fungsi dinamis
            let teksDetail = buatBarisRapi(
                `  ${item.qty} x ${formatRupiah(item.harga)}`,
                formatRupiah(item.subtotal),
                48
            );
            builder = builder.line(teksDetail);
        });

        // 5. Bagian Total, Bayar, Kembali
        let teksTotal = buatBarisRapi('Total', formatRupiah(data.total), 48);
        let teksBayar = buatBarisRapi('Bayar', formatRupiah(data.bayar), 48);
        let teksKembali = buatBarisRapi('Kembali', formatRupiah(data.kembalian), 48);

        builder = builder
            .line('    --------------------------------------------') // Garis pembatas bawah
            .line(teksTotal)
            .line(teksBayar)
            .line(teksKembali)
            .newline()
            .align('center')
            .line('Terima kasih!')
            .newline()
            .newline()
            .cut();

        const bytes = builder.encode();

        // 4. Kirim ke printer
        setStatus('Mencetak...');
        await printerConnection.print(bytes);

        setStatus('Berhasil dicetak!');
    } catch (err) {
        console.error(err);
        setStatus('Gagal mencetak: ' + err.message);
        alert(
            'Gagal mencetak: ' + err.message +
            '\n\nPastikan:\n' +
            '1. Bluetooth & lokasi (GPS) HP aktif\n' +
            '2. Pakai Chrome di Android\n' +
            '3. Printer dalam keadaan ON dan dekat HP'
        );
    }
}

function formatRupiah(angka) {
    return 'Rp' + Number(angka).toLocaleString('id-ID');
}
