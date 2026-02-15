<?php
require_once '../config.php';
requireLogin();
if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole());
}
redirect('/dashboard/freelancer/index');
