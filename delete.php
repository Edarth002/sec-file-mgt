<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT file_name, stored_path FROM documents WHERE id=?");
$stmt->bind_param("i",$id); $stmt->execute(); $res=$stmt->get_result();
if(!$row=$res->fetch_assoc()) { header("Location: files.php"); exit; }
$full = __DIR__ . '/' . $row['stored_path'];
if (file_exists($full)) unlink($full);
$del = $conn->prepare("DELETE FROM documents WHERE id=?");
$del->bind_param("i",$id); $del->execute();
$log = $conn->prepare("INSERT INTO logs(action,file_name,staff_id) VALUES ('delete',?,?)");
$log->bind_param("ss",$row['file_name'], $_SESSION['user']['staff_id']); $log->execute();
header("Location: files.php");
exit;
