// Data AgroVoice statis (pengganti MySQL untuk GitHub Pages)
const AGRO_DATA = {
    kategori: [
        { id: 1, nama: "AgriPerikanan", slug: "agri-perikanan", file_html: "uploads/web/agri-perikanan.html" },
        { id: 2, nama: "AgroPertanian", slug: "agro-pertanian", file_html: "uploads/web/1794879347_agropertanian.html" },
        { id: 3, nama: "AgroPeternakan", slug: "agro-peternakan", file_html: "uploads/web/1794879300_agropeternakan.html" }
    ],
    berita: [
        {
            id: 1,
            judul: "Penyuluh dan petani mencatat panen lewat ponsel",
            ringkasan: "Pendampingan di sawah kini lebih ringan: data panen dan jadwal tanam dicatat langsung dari lahan.",
            isi: "Pendampingan di sawah kini jauh lebih efektif berkat digitalisasi pelaporan panen. Melalui ponsel pintar, petani dan petugas penyuluh pertanian lapangan (PPL) kini dapat mencatat volume hasil panen, kondisi kelembaban tanah, hingga jadwal pemupukan secara real-time langsung dari pematang sawah.\n\nSistem ini tidak hanya memangkas birokrasi pencatatan manual, namun juga memberikan kepastian data pasokan pangan ke pasar dan dinas terkait. Di masa depan, integrasi sistem pemantauan panen ini akan terus diperluas untuk mendukung ketahanan pangan nasional secara mandiri.",
            gambar: "1794861598_berita test.jpg",
            kategori_id: null,
            kategori_nama: "Umum",
            tanggal: "2026-11-17 08:00:00"
        },
        {
            id: 2,
            judul: "Pakan otomatis membantu pembudidaya ikan",
            ringkasan: "Alat pakan otomatis membuat jadwal beri makan lebih teratur dan mengurangi pakan terbuang.",
            isi: "Pemanfaatan alat auto-feeder atau pemberi pakan otomatis berbasis sensor pintar mulai dirasakan dampaknya oleh para pembudidaya ikan air tawar dan tambak pesisir. Dengan alat ini, frekuensi dan takaran pakan dapat disesuaikan dengan nafsu makan dan bobot ikan secara presisi.\n\nSelain meminimalisir pakan yang tenggelam sia-sia dan mengotori dasar kolam (FCR lebih efisien), alat ini membebaskan petambak dari keterikatan jam pemberian pakan manual, sehingga produktivitas dan keuntungan panen meningkat signifikan.",
            gambar: "1794872538_agri-perikanan.jpg",
            kategori_id: 1,
            kategori_nama: "AgriPerikanan",
            tanggal: "2026-11-16 10:30:00"
        },
        {
            id: 3,
            judul: "Musim panen: apa yang perlu dicatat petani",
            ringkasan: "Daftar singkat data panen yang berguna untuk menghitung biaya dan menentukan jadwal tanam berikutnya.",
            isi: "Memasuki periode panen raya, pencatatan data pertanian seringkali terabaikan padahal memegang peranan krusial bagi keuntungan usaha tani. Tiga parameter penting yang wajib dicatat antara lain: total bobot panen riil, ongkos tenaga kerja pemanenan, serta persentase kehilangan hasil (losses) selama perontokan.\n\nDengan memiliki rekam jejak tertulis atau digital, petani memiliki posisi tawar yang jauh lebih kuat ketika berhadapan dengan pedagang serta dapat menghitung laba bersih usaha taninya dengan akurat.",
            gambar: "1794861001_panen.jpg",
            kategori_id: null,
            kategori_nama: "Umum",
            tanggal: "2026-11-15 14:15:00"
        }
    ],
    galeri: [
        {
            id: 1,
            kategori_id: null,
            tipe: "foto",
            file: "1794861001_panen.jpg",
            url_file: "uploads/1794861001_panen.jpg",
            judul: "Panen Padi Raya di Jawa Barat",
            tanggal: "2026-11-17 07:00:00"
        },
        {
            id: 2,
            kategori_id: null,
            tipe: "foto",
            file: "1794872361_ayam.jpg",
            url_file: "uploads/1794872361_ayam.jpg",
            judul: "Peternakan Ayam Modern",
            tanggal: "2026-11-16 16:00:00"
        },
        {
            id: 3,
            kategori_id: 1,
            tipe: "foto",
            file: "1794872538_agri-perikanan.jpg",
            url_file: "uploads/1794872538_agri-perikanan.jpg",
            judul: "Budidaya Ikan Kolam Bioflok",
            tanggal: "2026-11-15 09:30:00"
        },
        {
            id: 4,
            kategori_id: null,
            tipe: "foto",
            file: "1794880994_bapak1.jpg",
            url_file: "uploads/1794880994_bapak1.jpg",
            judul: "Penyuluhan Petani Lapangan",
            tanggal: "2026-11-14 11:00:00"
        }
    ]
};

// Make available globally
if (typeof window !== 'undefined') {
    window.AGRO_DATA = AGRO_DATA;
}
