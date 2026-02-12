<?php
require_once '../config.php';
requireLogin();
if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole() . '.php');
}
redirect('/dashboard/freelancer/index.php');
