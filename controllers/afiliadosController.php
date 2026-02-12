<?php

    class AfiliadosController
    {
        public static function getRegistros($perfil, $nombre, $usuario)
        {
            require_once ROOT_PATH . '/config/database.php';
            $conn = conn();

            $rolesPermitidos = [
                'Adminis','Supervi','Avisual','Snutric',
                'Afisica','Ccorpor','Tmuestr','Svitale','Pulmvit'
            ];


            if (!in_array($perfil, $rolesPermitidos)) {
                return [];
            }            


            $sql = "SELECT * FROM pacientes";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Error SQL: " . $conn->error);
            }

            $stmt->execute();

            $result = $stmt->get_result();
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            return $data;
        }
    }

?>