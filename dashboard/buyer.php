<?php
require_once '../config.php';
requireLogin();
if (getUserRole() !== 'buyer') {
    redirect('/dashboard/' . getUserRole() . '.php');
}
redirect('/dashboard/buyer/index.php');
