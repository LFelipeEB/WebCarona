<?php
require_once '../Carona/Carona.class.php';

$c = new Carona(1, 1, 3, '13:00', '25/02/2016', 1.5, 1, 'Carona Teste', 'Largo', 'Campus 2', 'Passo pelo amendoim');
echo $c;