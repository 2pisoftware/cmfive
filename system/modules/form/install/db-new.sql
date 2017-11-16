-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 13, 2017 at 04:56 PM
-- Server version: 5.7.20-0ubuntu0.17.10.1
-- PHP Version: 5.6.32-1+ubuntu17.10.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmfive`
--

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE `form` (
  `id` bigint(20) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `header_template` varchar(255) DEFAULT NULL,
  `row_template` varchar(255) DEFAULT NULL,
  `summary_template` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_application`
--

CREATE TABLE `form_application` (
  `id` bigint(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_application_mapping`
--

CREATE TABLE `form_application_mapping` (
  `id` bigint(20) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_application_member`
--

CREATE TABLE `form_application_member` (
  `id` bigint(20) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `member_user_id` bigint(20) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'VIEWER',
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_application_view`
--

CREATE TABLE `form_application_view` (
  `id` bigint(20) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `form_id` bigint(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `template_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_field`
--

CREATE TABLE `form_field` (
  `id` bigint(20) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `name` varchar(256) NOT NULL,
  `technical_name` varchar(256) DEFAULT NULL,
  `interface_class` varchar(256) DEFAULT NULL,
  `type` varchar(256) NOT NULL,
  `mask` varchar(1024) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_field_metadata`
--

CREATE TABLE `form_field_metadata` (
  `id` bigint(20) NOT NULL,
  `form_field_id` bigint(20) DEFAULT NULL,
  `meta_key` varchar(256) NOT NULL,
  `meta_value` varchar(256) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_instance`
--

CREATE TABLE `form_instance` (
  `id` bigint(20) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `object_class` varchar(256) NOT NULL,
  `object_id` bigint(20) NOT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_mapping`
--

CREATE TABLE `form_mapping` (
  `id` bigint(20) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `object` varchar(256) NOT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_value`
--

CREATE TABLE `form_value` (
  `id` bigint(20) NOT NULL,
  `form_instance_id` bigint(20) NOT NULL,
  `form_field_id` bigint(20) NOT NULL,
  `value` varchar(1024) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `form`
--
ALTER TABLE `form`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`),
  ADD KEY `is_deleted_2` (`is_deleted`);

--
-- Indexes for table `form_application`
--
ALTER TABLE `form_application`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`);

--
-- Indexes for table `form_application_mapping`
--
ALTER TABLE `form_application_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`);

--
-- Indexes for table `form_application_member`
--
ALTER TABLE `form_application_member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`);

--
-- Indexes for table `form_application_view`
--
ALTER TABLE `form_application_view`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`);

--
-- Indexes for table `form_field`
--
ALTER TABLE `form_field`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `ordering` (`ordering`),
  ADD KEY `is_deleted_2` (`is_deleted`);

--
-- Indexes for table `form_field_metadata`
--
ALTER TABLE `form_field_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`),
  ADD KEY `form_field_id` (`form_field_id`),
  ADD KEY `is_deleted_2` (`is_deleted`);

--
-- Indexes for table `form_instance`
--
ALTER TABLE `form_instance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `object_class` (`object_class`),
  ADD KEY `object_id` (`object_id`),
  ADD KEY `is_deleted_2` (`is_deleted`);

--
-- Indexes for table `form_mapping`
--
ALTER TABLE `form_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `is_deleted_2` (`is_deleted`);

--
-- Indexes for table `form_value`
--
ALTER TABLE `form_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`),
  ADD KEY `form_instance_id` (`form_instance_id`),
  ADD KEY `form_field_id` (`form_field_id`),
  ADD KEY `is_deleted_2` (`is_deleted`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `form`
--
ALTER TABLE `form`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `form_application`
--
ALTER TABLE `form_application`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `form_application_mapping`
--
ALTER TABLE `form_application_mapping`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `form_application_member`
--
ALTER TABLE `form_application_member`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_application_view`
--
ALTER TABLE `form_application_view`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_field`
--
ALTER TABLE `form_field`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `form_field_metadata`
--
ALTER TABLE `form_field_metadata`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `form_instance`
--
ALTER TABLE `form_instance`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;
--
-- AUTO_INCREMENT for table `form_mapping`
--
ALTER TABLE `form_mapping`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `form_value`
--
ALTER TABLE `form_value`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=886;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
