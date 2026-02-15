<?php
require_once '../config.php';
requireLogin();
if (getUserRole() !== 'agency') {
    redirect('/dashboard/' . getUserRole());
}
redirect('/dashboard/agency/index');
