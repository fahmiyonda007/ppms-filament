<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // DB::statement("INSERT INTO `coa_level_firsts` VALUES (1, '1', 'AKTIVA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL); ");
        DB::statement("INSERT INTO `coa_level_firsts` VALUES (2, '2', 'KEWAJIBAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_firsts` VALUES (3, '3', 'EKUITAS', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_firsts` VALUES (4, '4', 'PENDAPATAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_firsts` VALUES (5, '5', 'BEBAN / BIAYA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");

        DB::statement("INSERT INTO `coa_level_seconds` VALUES (1, 1, '01', 'KAS', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (2, 1, '02', 'DEPOSIT', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (3, 2, '01', 'KEWAJIBAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (4, 3, '01', 'MODAL / EKUITAS PEMILIK', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (5, 4, '01', 'PENDAPATAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (6, 5, '01', 'BIAYA LEGALITAS', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (7, 5, '02', 'BIAYA MATERIAL BANGUNAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (8, 5, '03', 'BIAYA MATERIAL FINISHING', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (9, 5, '04', 'BIAYA OPERASIONAL LAINNYA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (10, 5, '05', 'BIAYA SUMBER DAYA MANUSIA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (11, 5, '99', 'BIAYA PERSONAL', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL);");
        DB::statement("INSERT INTO `coa_level_seconds` VALUES (12, 1, '03', 'PIUTANG', '2023-04-30 11:09:07', '2023-04-30 11:09:07', 'sa@ppms.id', NULL);");

        DB::statement("INSERT INTO `coa_level_thirds` VALUES (1, 1, 1, '101001', 'KAS BCA - OKTI', '2023-04-29 19:18:04', '2023-06-08 16:57:16', 'sa@ppms.id', 'sa@ppms.id', 3500000, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (4, 1, 1, '101004', 'KAS TUNAI', '2023-04-29 19:18:04', '2023-05-27 13:16:02', 'sa@ppms.id', 'sa@ppms.id', 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (5, 1, 2, '102001', 'DEPOSIT TOKO', '2023-04-29 19:18:04', '2023-05-27 13:17:37', 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (6, 3, 4, '301001', 'MODAL PEMILIK', '2023-04-29 19:18:04', '2023-06-07 15:52:43', 'sa@ppms.id', NULL, 1500000, 'C');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (7, 4, 5, '401001', 'PEND. JUAL BELI', '2023-04-29 19:18:04', '2023-05-27 10:04:15', 'sa@ppms.id', NULL, 0, 'C');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (9, 5, 6, '501001', 'BIAYA SHM', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (10, 5, 6, '501002', 'BIAYA IMB', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (11, 5, 6, '501003', 'BIAYA NOTARIS', '2023-04-29 19:18:04', '2023-06-07 14:48:56', 'sa@ppms.id', NULL, 52000000, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (12, 5, 6, '501004', 'BIAYA PAJAK', '2023-04-29 19:18:04', '2023-06-07 14:48:56', 'sa@ppms.id', NULL, 139520000, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (13, 5, 6, '501005', 'BIAYA LEGALITAS LAINNYA', '2023-04-29 19:18:04', '2023-04-30 20:37:27', 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (14, 5, 7, '502001', 'BIAYA BAJA RINGAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (15, 5, 7, '502002', 'BIAYA BATA MERAH', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (16, 5, 7, '502003', 'BIAYA BATU', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (17, 5, 7, '502004', 'BIAYA BESI', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (18, 5, 7, '502005', 'BIAYA HEBEL', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (19, 5, 7, '502006', 'BIAYA KAYU, TRIPLEK & BAMBU ', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (20, 5, 7, '502007', 'BIAYA PAKU', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (21, 5, 7, '502008', 'BIAYA PASIR', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (22, 5, 7, '502009', 'BIAYA PIPA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (23, 5, 7, '502010', 'BIAYA SEMEN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (24, 5, 7, '502011', 'BIAYA URUGAN TANAH', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (25, 5, 7, '502012', 'BIAYA WIREMESH', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (26, 5, 7, '502013', 'BIAYA BONDEK', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (27, 5, 7, '502014', 'BIAYA CEKER AYAM & RING', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (28, 5, 7, '502015', 'BIAYA MATERIAL BANGUNAN LAINNYA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (29, 5, 8, '503001', 'BIAYA KACA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (30, 5, 8, '503002', 'BIAYA PINTU', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (31, 5, 8, '503003', 'BIAYA JENDELA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (32, 5, 8, '503004', 'BIAYA LAMPU', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (33, 5, 8, '503005', 'BIAYA KABEL', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (34, 5, 8, '503006', 'BIAYA DAYA LISTRIK', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (35, 5, 8, '503007', 'BIAYA POMPA AIR', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (36, 5, 8, '503008', 'BIAYA GENTENG ATAP', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (37, 5, 8, '503009', 'BIAYA PLAFOND', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (38, 5, 8, '503010', 'BIAYA KERAMIK & GRANIT', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (39, 5, 8, '503011', 'BIAYA CAT', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (40, 5, 8, '503012', 'BIAYA KITCHEN SET', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (41, 5, 8, '503013', 'BIAYA PAGAR', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (42, 5, 8, '503014', 'BIAYA TERALIS', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (43, 5, 8, '503015', 'BIAYA KANOPI', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (44, 5, 8, '503016', 'BIAYA HAND RAILING', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (45, 5, 8, '503017', 'BIAYA CLOSET', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (46, 5, 8, '503018', 'BIAYA WASTAFEL', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (47, 5, 8, '503019', 'BIAYA SHOWER', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (48, 5, 8, '503020', 'BIAYA TOREN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (49, 5, 8, '503021', 'BIAYA KOLAM RENANG', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (50, 5, 8, '503022', 'BIAYA MATERIAL FINISHING LAINNYA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (51, 5, 9, '504001', 'BIAYA SEWA ALAT', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (52, 5, 9, '504002', 'BIAYA SEWA GEDUNG', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (53, 5, 9, '504003', 'BIAYA DEPOSIT TOKO', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (54, 5, 9, '504004', 'BIAYA KOORDINASI LINGKUNGAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (55, 5, 9, '504005', 'BIAYA OPERASIONAL LAINNYA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (56, 5, 9, '504006', 'BIAYA PEKERJAAN SAFETY TANK', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (57, 5, 9, '504007', 'BIAYA PEKERJAAN PIHAK KETIGA', '2023-04-29 19:18:04', '2023-06-06 15:17:44', 'sa@ppms.id', NULL, 34800000, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (58, 5, 9, '504008', 'BIAYA PEKERJAAN INFRA KEAMANAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (59, 5, 9, '504009', 'BIAYA PEKERJAAN PAGAR CLUSTER', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (60, 5, 9, '504010', 'BIAYA PEKERJAAN PENERANGAN JALAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (61, 5, 9, '504011', 'BIAYA PEKERJAAN PENGURUKAN & JALAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (62, 5, 9, '504012', 'BIAYA PEKERJAAN RESAPAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (63, 5, 9, '504013', 'BIAYA PEKERJAAN SUMUR', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (64, 5, 9, '504014', 'BIAYA MATERAI', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (65, 5, 9, '504015', 'BIAYA RAPAT KERJA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (66, 5, 9, '504016', 'BIAYA PROMOSI', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (67, 5, 9, '504017', 'BIAYA SUMBANGAN EKSTERNAL', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (68, 5, 9, '504018', 'BIAYA ALAT TULIS KANTOR', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (69, 5, 9, '504019', 'BIAYA INVENTARIS KANTOR', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (70, 5, 9, '504020', 'BIAYA PEMBELIAN LAHAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (71, 5, 10, '505001', 'GAJI DAN UPAH', '2023-04-29 19:18:04', '2023-06-03 08:20:39', 'sa@ppms.id', NULL, 1345000, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (76, 5, 10, '505006', 'BIAYA KOMISI AGENT', '2023-04-29 19:18:04', '2023-06-07 14:48:56', 'sa@ppms.id', NULL, 54770000, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (77, 5, 10, '505007', 'BIAYA KOMISI AGENT LAINNYA', '2023-04-29 19:18:04', '2023-06-07 14:48:56', 'sa@ppms.id', NULL, 60000000, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (78, 5, 10, '505008', 'BIAYA SERAGAM', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (79, 5, 10, '505009', 'BIAYA TUNJANGAN HARI RAYA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (80, 5, 10, '505010', 'BIAYA TUNJANGAN PERFORMANCE', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (81, 5, 10, '505011', 'BIAYA TUNJANGAN TRANSPORTASI', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (82, 5, 10, '505012', 'BIAYA TUNJANGAN KOMSUMTIF', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (83, 5, 10, '505013', 'BIAYA TUNJANGAN KESEHATAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (84, 5, 10, '505014', 'BIAYA TUNJANGAN LAINNYA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (85, 5, 10, '505015', 'BIAYA BAHAN BAKAR', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (86, 5, 10, '505016', 'BIAYA PELATIHAN', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (87, 5, 10, '505017', 'BIAYA TENAGA KERJA LAINNYA', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (88, 5, 11, '599001', 'BIAYA PRIBADI', '2023-04-29 19:18:04', NULL, 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (89, 1, 12, '103001', 'KASBON KARYAWAN', '2023-04-30 11:09:55', '2023-06-08 16:57:16', 'sa@ppms.id', NULL, -550000, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (90, 1, 2, '102002', 'DEPOSIT OMA', '2023-04-30 16:15:53', '2023-04-30 16:58:06', 'sa@ppms.id', NULL, 0, 'D');");
        DB::statement("INSERT INTO `coa_level_thirds` VALUES (91, 1, 1, '101005', 'KAS BCA - BOS ANDRI', '2023-05-27 04:57:29', '2023-05-27 05:01:39', 'sa@ppms.id', NULL, 0, 'D');");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("truncate table coa_level_firsts");
    }
};
