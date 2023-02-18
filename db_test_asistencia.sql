-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-02-2023 a las 19:49:05
-- Versión del servidor: 10.4.20-MariaDB
-- Versión de PHP: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_test_asistencia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `id_gerencia` int(11) DEFAULT NULL,
  `area` varchar(150) NOT NULL,
  `hora_ingreso` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `first_out_group` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas_sedes`
--

CREATE TABLE `areas_sedes` (
  `id` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `id_proceso` int(11) NOT NULL,
  `c_costo` varchar(200) NOT NULL DEFAULT '[]'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aux_holidays`
--

CREATE TABLE `aux_holidays` (
  `id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `aux_holidays`
--

INSERT INTO `aux_holidays` (`id`, `day`, `month`, `year`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, '2020-06-13 13:54:51', '2020-06-13 13:54:51', NULL),
(2, 9, 3, NULL, '2020-06-13 13:55:30', '2020-06-13 13:55:30', NULL),
(3, 10, 3, NULL, '2020-06-13 13:55:37', '2020-06-13 13:55:37', NULL),
(4, 8, 12, 0, '2020-11-27 16:58:55', '2020-11-27 16:58:55', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aux_suplencia`
--

CREATE TABLE `aux_suplencia` (
  `id` int(11) NOT NULL,
  `code` varchar(5) NOT NULL,
  `h_goin` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aux_type_doc`
--

CREATE TABLE `aux_type_doc` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aux_type_reg`
--

CREATE TABLE `aux_type_reg` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `aditionable` int(11) NOT NULL,
  `abr` varchar(50) NOT NULL,
  `is_paid` int(11) NOT NULL DEFAULT 1,
  `codigo` varchar(6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employes`
--

CREATE TABLE `employes` (
  `id` int(11) NOT NULL,
  `synchronized_users` varchar(20) DEFAULT '[]',
  `id_employe_type` int(11) DEFAULT 1,
  `dir_ind` varchar(50) NOT NULL DEFAULT 'DIRECTO',
  `fullname` varchar(150) DEFAULT NULL,
  `hasChildren` int(11) DEFAULT NULL,
  `telephone_num` varchar(12) DEFAULT NULL,
  `doc_num` varchar(11) DEFAULT NULL,
  `code` varchar(6) DEFAULT NULL,
  `valid` int(11) NOT NULL DEFAULT 0,
  `id_proceso` int(11) NOT NULL,
  `id_function` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `id_sede` int(11) NOT NULL DEFAULT 1,
  `turno` varchar(50) NOT NULL DEFAULT 'DIA',
  `remuneracion` double(8,2) NOT NULL,
  `c_costo` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employes_type`
--

CREATE TABLE `employes_type` (
  `id` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups`
--

CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `permission` text CHARACTER SET latin1 DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `permission`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Admin', 'a:71:{i:0;s:8:\"viewMant\";i:1;s:11:\"createSedes\";i:2;s:11:\"updateSedes\";i:3;s:9:\"viewSedes\";i:4;s:11:\"deleteSedes\";i:5;s:14:\"createGerencia\";i:6;s:14:\"updateGerencia\";i:7;s:12:\"viewGerencia\";i:8;s:14:\"deleteGerencia\";i:9;s:10:\"createArea\";i:10;s:10:\"updateArea\";i:11;s:8:\"viewArea\";i:12;s:10:\"deleteArea\";i:13;s:13:\"createFuncion\";i:14;s:13:\"updateFuncion\";i:15;s:11:\"viewFuncion\";i:16;s:13:\"deleteFuncion\";i:17;s:14:\"createProcesos\";i:18;s:14:\"updateProcesos\";i:19;s:12:\"viewProcesos\";i:20;s:14:\"deleteProcesos\";i:21;s:7:\"viewAux\";i:22;s:10:\"createTreg\";i:23;s:10:\"updateTreg\";i:24;s:8:\"viewTreg\";i:25;s:10:\"deleteTreg\";i:26;s:10:\"createTdoc\";i:27;s:10:\"updateTdoc\";i:28;s:8:\"viewTdoc\";i:29;s:10:\"deleteTdoc\";i:30;s:12:\"createRsuple\";i:31;s:12:\"updateRsuple\";i:32;s:10:\"viewRsuple\";i:33;s:12:\"deleteRsuple\";i:34;s:13:\"viewGEmployes\";i:35;s:14:\"createEmployes\";i:36;s:14:\"updateEmployes\";i:37;s:12:\"viewEmployes\";i:38;s:14:\"deleteEmployes\";i:39;s:20:\"createProcessEmploye\";i:40;s:20:\"updateProcessEmploye\";i:41;s:18:\"viewProcessEmploye\";i:42;s:20:\"deleteProcessEmploye\";i:43;s:7:\"viewGHA\";i:44;s:8:\"createHA\";i:45;s:8:\"updateHA\";i:46;s:6:\"viewHA\";i:47;s:8:\"deleteHA\";i:48;s:12:\"viewHorarios\";i:49;s:14:\"createHorarios\";i:50;s:14:\"updateHorarios\";i:51;s:12:\"viewHorarios\";i:52;s:14:\"deleteHorarios\";i:53;s:12:\"viewReportes\";i:54;s:17:\"createRAssistance\";i:55;s:17:\"updateRAssistance\";i:56;s:15:\"viewRAssistance\";i:57;s:17:\"deleteRAssistance\";i:58;s:15:\"createREmployes\";i:59;s:15:\"updateREmployes\";i:60;s:13:\"viewREmployes\";i:61;s:15:\"deleteREmployes\";i:62;s:13:\"viewGUsuarios\";i:63;s:16:\"createPermission\";i:64;s:16:\"updatePermission\";i:65;s:14:\"viewPermission\";i:66;s:16:\"deletePermission\";i:67;s:11:\"createUsers\";i:68;s:11:\"updateUsers\";i:69;s:9:\"viewUsers\";i:70;s:11:\"deleteUsers\";}', '2020-01-14 23:22:27', '2020-06-15 16:27:05', NULL),
(4, 'Demo', 'a:7:{i:0;s:8:\"viewMant\";i:1;s:7:\"viewAux\";i:2;s:13:\"viewGEmployes\";i:3;s:7:\"viewGHA\";i:4;s:12:\"viewHorarios\";i:5;s:12:\"viewReportes\";i:6;s:13:\"viewGUsuarios\";}', '2020-01-14 23:26:36', '2020-06-02 05:33:45', NULL),
(17, 'Prueba', 'a:64:{i:0;s:8:\"viewMant\";i:1;s:11:\"createSedes\";i:2;s:11:\"updateSedes\";i:3;s:9:\"viewSedes\";i:4;s:11:\"deleteSedes\";i:5;s:14:\"createGerencia\";i:6;s:14:\"updateGerencia\";i:7;s:12:\"viewGerencia\";i:8;s:14:\"deleteGerencia\";i:9;s:10:\"createArea\";i:10;s:10:\"updateArea\";i:11;s:8:\"viewArea\";i:12;s:10:\"deleteArea\";i:13;s:13:\"createFuncion\";i:14;s:13:\"updateFuncion\";i:15;s:11:\"viewFuncion\";i:16;s:13:\"deleteFuncion\";i:17;s:14:\"createProcesos\";i:18;s:14:\"updateProcesos\";i:19;s:12:\"viewProcesos\";i:20;s:14:\"deleteProcesos\";i:21;s:7:\"viewAux\";i:22;s:10:\"createTreg\";i:23;s:10:\"updateTreg\";i:24;s:8:\"viewTreg\";i:25;s:10:\"deleteTreg\";i:26;s:10:\"createTdoc\";i:27;s:10:\"updateTdoc\";i:28;s:8:\"viewTdoc\";i:29;s:10:\"deleteTdoc\";i:30;s:12:\"createRsuple\";i:31;s:12:\"updateRsuple\";i:32;s:10:\"viewRsuple\";i:33;s:12:\"deleteRsuple\";i:34;s:14:\"createEmployes\";i:35;s:14:\"updateEmployes\";i:36;s:12:\"viewEmployes\";i:37;s:14:\"deleteEmployes\";i:38;s:20:\"createProcessEmploye\";i:39;s:20:\"updateProcessEmploye\";i:40;s:18:\"viewProcessEmploye\";i:41;s:20:\"deleteProcessEmploye\";i:42;s:7:\"viewGHA\";i:43;s:8:\"createHA\";i:44;s:8:\"updateHA\";i:45;s:6:\"viewHA\";i:46;s:8:\"deleteHA\";i:47;s:12:\"viewReportes\";i:48;s:17:\"createRAssistance\";i:49;s:17:\"updateRAssistance\";i:50;s:15:\"viewRAssistance\";i:51;s:17:\"deleteRAssistance\";i:52;s:15:\"createREmployes\";i:53;s:15:\"updateREmployes\";i:54;s:13:\"viewREmployes\";i:55;s:15:\"deleteREmployes\";i:56;s:16:\"createPermission\";i:57;s:16:\"updatePermission\";i:58;s:14:\"viewPermission\";i:59;s:16:\"deletePermission\";i:60;s:11:\"createUsers\";i:61;s:11:\"updateUsers\";i:62;s:9:\"viewUsers\";i:63;s:11:\"deleteUsers\";}', '2020-05-17 15:10:01', '2020-06-02 05:32:33', '2020-06-02 05:32:33'),
(18, 'Moderador', 'a:20:{i:0;s:13:\"viewGEmployes\";i:1;s:14:\"createEmployes\";i:2;s:14:\"updateEmployes\";i:3;s:12:\"viewEmployes\";i:4;s:20:\"createProcessEmploye\";i:5;s:20:\"updateProcessEmploye\";i:6;s:18:\"viewProcessEmploye\";i:7;s:7:\"viewGHA\";i:8;s:8:\"createHA\";i:9;s:8:\"updateHA\";i:10;s:6:\"viewHA\";i:11;s:8:\"deleteHA\";i:12;s:12:\"viewHorarios\";i:13;s:14:\"createHorarios\";i:14;s:14:\"updateHorarios\";i:15;s:12:\"viewHorarios\";i:16;s:14:\"deleteHorarios\";i:17;s:12:\"viewReportes\";i:18;s:17:\"updateRAssistance\";i:19;s:15:\"viewRAssistance\";}', '2020-05-30 22:04:12', '2020-07-29 12:01:50', NULL),
(19, 'Supervisor de Area - Verificador', 'a:1:{i:0;s:13:\"viewREmployes\";}', '2020-05-30 22:15:25', '2020-09-17 17:42:42', NULL),
(20, 'Gerente', 'a:2:{i:0;s:13:\"viewRGerencia\";i:1;s:12:\"viewReportes\";}', '2020-05-31 00:27:14', '2020-07-08 14:06:24', NULL),
(23, 'Jefes de Area', 'a:2:{i:0;s:12:\"viewReportes\";i:1;s:15:\"viewRAssistance\";}', '2020-07-08 14:07:35', '2020-07-08 14:07:35', NULL),
(24, 'Backups', 'N;', '2020-07-11 11:53:29', '2020-07-11 11:53:29', NULL),
(25, 'SALIDAS', 'a:2:{i:0;s:14:\"viewAsistencia\";i:1;s:14:\"registerSalida\";}', '2020-07-14 01:15:50', '2020-07-14 01:15:50', NULL),
(26, 'INGRESOS', 'a:2:{i:0;s:15:\"registerIngreso\";i:1;s:14:\"viewAsistencia\";}', '2020-07-14 01:16:06', '2020-07-14 01:16:06', NULL),
(27, 'Gerente General', 'a:1:{i:0;s:20:\"viewRGerenciaGeneral\";}', '2020-07-17 21:37:11', '2020-07-17 21:37:11', NULL),
(28, 'Gerente RRHH', 'a:1:{i:0;s:21:\"viewRGerenciaRecursos\";}', '2020-07-17 21:37:26', '2020-07-18 15:35:54', NULL),
(29, 'VALIDADOR', 'a:3:{i:0;s:16:\"viewValidaciones\";i:1;s:14:\"updateEmployes\";i:2;s:8:\"updateHA\";}', '2020-09-16 21:54:37', '2020-11-03 19:10:34', NULL),
(30, 'GERENTE PRODUCCION', 'a:1:{i:0;s:23:\"viewRGerenciaProduccion\";}', '2021-01-19 21:20:47', '2021-01-19 21:20:47', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procesos`
--

CREATE TABLE `procesos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procesos_employe`
--

CREATE TABLE `procesos_employe` (
  `id` int(11) NOT NULL,
  `id_employe` int(11) NOT NULL,
  `id_proceso` int(11) NOT NULL,
  `until_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_assistance`
--

CREATE TABLE `reg_assistance` (
  `id` int(10) UNSIGNED NOT NULL,
  `sync_id` varchar(20) DEFAULT NULL,
  `code` varchar(8) DEFAULT NULL,
  `synchronized_users` varchar(50) DEFAULT '[]',
  `id_employe` int(11) DEFAULT NULL,
  `id_aux_treg` int(11) NOT NULL DEFAULT 10,
  `id_function` int(11) DEFAULT NULL,
  `id_sede` int(11) DEFAULT NULL,
  `id_employe_type` int(11) DEFAULT NULL,
  `id_proceso` int(11) DEFAULT NULL,
  `dir_ind` varchar(20) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `turno` varchar(20) DEFAULT NULL,
  `c_costo` varchar(50) DEFAULT NULL,
  `temperature` double NOT NULL DEFAULT 0,
  `checked` tinyint(1) NOT NULL DEFAULT 0,
  `checked_at` timestamp NULL DEFAULT NULL,
  `basico` double(8,2) NOT NULL DEFAULT 0.00,
  `paga` double(8,2) NOT NULL DEFAULT 0.00,
  `paga_descanso` double(8,2) NOT NULL DEFAULT 0.00,
  `paga_bono_familiar` double(8,2) NOT NULL DEFAULT 0.00,
  `prima_produccion` double(8,2) NOT NULL DEFAULT 0.00,
  `paga_25` double(8,2) NOT NULL DEFAULT 0.00,
  `paga_35` double(8,2) NOT NULL DEFAULT 0.00,
  `paga_nocturna` double(8,2) NOT NULL DEFAULT 0.00,
  `paga_100` double(8,2) NOT NULL DEFAULT 0.00,
  `horas_trabajadas` varchar(8) NOT NULL DEFAULT '00:00:00',
  `horas_prima_produccion` varchar(8) NOT NULL DEFAULT '00:00:00',
  `horas_25` varchar(8) NOT NULL DEFAULT '00:00:00',
  `horas_35` varchar(8) NOT NULL DEFAULT '00:00:00',
  `horas_nocturna` varchar(8) NOT NULL DEFAULT '00:00:00',
  `horas_100` varchar(8) NOT NULL DEFAULT '00:00:00',
  `horas_descontadas` varchar(8) NOT NULL DEFAULT '00:00:00',
  `hasDinner` tinyint(1) NOT NULL DEFAULT 1,
  `hasObs` bit(1) NOT NULL DEFAULT b'1',
  `id_user_checked` int(11) DEFAULT NULL,
  `lastRegister` bit(1) NOT NULL DEFAULT b'0',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at_search` timestamp NULL DEFAULT current_timestamp(),
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `deletedAt` timestamp NULL DEFAULT NULL,
  `uniqueReg` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_employes`
--

CREATE TABLE `reg_employes` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  `sede_id` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_horarios`
--

CREATE TABLE `reg_horarios` (
  `id` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL DEFAULT 'green',
  `since_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `until_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_javas`
--

CREATE TABLE `reg_javas` (
  `id` int(11) NOT NULL,
  `id_employe` int(11) NOT NULL,
  `des_presentation` varchar(255) NOT NULL,
  `presentation_code` varchar(125) NOT NULL,
  `weight` double NOT NULL,
  `costo` double NOT NULL,
  `nro_lote` varchar(255) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_unchecked`
--

CREATE TABLE `reg_unchecked` (
  `id` int(11) NOT NULL,
  `reg_code` varchar(20) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_sede` int(11) DEFAULT NULL,
  `id_proceso` int(11) DEFAULT NULL,
  `message` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id` int(11) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `zona` varchar(50) DEFAULT NULL,
  `t_sede` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `email`, `email_verified_at`, `password`, `address`, `city`, `country`, `postal`, `about`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin', 'Admin', 'admin@argon.com', NULL, '$2y$10$XbUQZGyuiWlVjZXLXzZUwegpg8PPfqhzoA50AGswRDGM1i8QpdQia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-18 18:10:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_config`
--

CREATE TABLE `users_config` (
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `show_aux_treg` varchar(255) DEFAULT NULL,
  `show_areas` varchar(1000) DEFAULT NULL,
  `show_function` varchar(1000) DEFAULT '[]',
  `sedes` varchar(255) DEFAULT NULL,
  `id_group` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `users_config`
--

INSERT INTO `users_config` (`id_user`, `show_aux_treg`, `show_areas`, `show_function`, `sedes`, `id_group`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\"]', '[\"1\",\"2\",\"3\",\"4\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\",\"13\",\"14\",\"16\",\"17\",\"19\",\"20\",\"21\",\"22\",\"23\",\"24\",\"25\",\"26\",\"27\",\"28\",\"29\",\"30\"]', '[]', '[\"1\",\"2\",\"9\",\"16\"]', 2, '2020-05-11 20:51:18', '2020-05-11 20:51:18', NULL),
(58, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\"]', 'null', '[]', '[\"18\"]', 18, '2023-02-17 17:17:51', '2023-02-17 17:17:51', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `areas_sedes`
--
ALTER TABLE `areas_sedes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `aux_holidays`
--
ALTER TABLE `aux_holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `aux_suplencia`
--
ALTER TABLE `aux_suplencia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `aux_type_doc`
--
ALTER TABLE `aux_type_doc`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `aux_type_reg`
--
ALTER TABLE `aux_type_reg`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `employes`
--
ALTER TABLE `employes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doc_num` (`doc_num`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `employes_type`
--
ALTER TABLE `employes_type`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `procesos`
--
ALTER TABLE `procesos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `procesos_employe`
--
ALTER TABLE `procesos_employe`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reg_assistance`
--
ALTER TABLE `reg_assistance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reg` (`uniqueReg`);

--
-- Indices de la tabla `reg_employes`
--
ALTER TABLE `reg_employes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reg_horarios`
--
ALTER TABLE `reg_horarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reg_javas`
--
ALTER TABLE `reg_javas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reg_unchecked`
--
ALTER TABLE `reg_unchecked`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `users_config`
--
ALTER TABLE `users_config`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `areas_sedes`
--
ALTER TABLE `areas_sedes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aux_holidays`
--
ALTER TABLE `aux_holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `aux_suplencia`
--
ALTER TABLE `aux_suplencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aux_type_doc`
--
ALTER TABLE `aux_type_doc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aux_type_reg`
--
ALTER TABLE `aux_type_reg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `employes_type`
--
ALTER TABLE `employes_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `procesos`
--
ALTER TABLE `procesos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `procesos_employe`
--
ALTER TABLE `procesos_employe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reg_assistance`
--
ALTER TABLE `reg_assistance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reg_employes`
--
ALTER TABLE `reg_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reg_horarios`
--
ALTER TABLE `reg_horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reg_javas`
--
ALTER TABLE `reg_javas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reg_unchecked`
--
ALTER TABLE `reg_unchecked`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `users_config`
--
ALTER TABLE `users_config`
  MODIFY `id_user` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
