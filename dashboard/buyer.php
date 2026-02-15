<?php
require_once '../config.php';
requireLogin();
if (getUserRole() !== 'buyer') {
    redirect('/dashboard/' . getUserRole());
}
redirect('/dashboard/buyer/index');
