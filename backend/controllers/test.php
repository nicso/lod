<?php
// backend/public/index.php

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Content-Type: application/json');

echo json_encode(['message' => 'Hello World!']);