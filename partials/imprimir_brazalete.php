<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimiendo brazalete</title>

    <?php require_once __DIR__ . '/../config/config.php'; ?>
    <!-- Text2Barcode (OFICIAL) -->
    <script src="https://labeldictate.com/text2barcode/lib/t2bprinter.js"></script>
</head>

<body>
    <p>Imprimiendo brazalete…</p>

    <script>
        (async () => {
            // 1️⃣ Obtener ID desde la URL
            const params = new URLSearchParams(window.location.search);
            const id = params.get("id");

            if (!id) {
                alert("ID no recibido");
                return;
            }

            // 2️⃣ Obtener impresoras
            const printers = await T2bPrinter.available();
            if (!printers.printer?.length) {
                alert("No hay impresoras disponibles");
                return;
            }

            // 👉 Seleccionar la impresora 

            const printer = printers.printer.find(p => p.name.includes("Ribetec_USB"));
            if (!printer) {
                alert("No se encontró la impresora Ribetec");
                return;
            }

            // 3️⃣ Obtener datos del paciente (PHP)
            const res = await fetch(`<?= BASE_URL ?>/ajax/imprimir_pacientes.php?id=${id}`);
            const data = await res.json();

            // 4️⃣ Construir ZPL (VISIBLE – SIN ROTAR)
            const zpl = `
                ^XA
                ^CI28
                ^PW480
                ^LL600
                ^MMT
                ^MNM

                ; ===== NOMBRE PEQUEÑO =====
                ^FO320,330
                ^A0R,16,16
                ^FD${data.nombre}^FS

                ; ===== FECHA =====
                ^FO290,380
                ^A0R,16,16
                ^FD${data.fecha}^FS

                ; ===== BARCODE ROTADO (Id) =====
                ^BY2,2,60
                ^FO220,300
                ^BCR,60,N,N,N
                ^FD${data.id}^FS

                ; ===== CURP =====
                ^FO190,320
                ^A0R,16,16
                ^FD${data.curp}^FS

                ^PQ1
                ^XZ
            `;

            // 5️⃣ Imprimir
            await T2bPrinter.write(printer, zpl);

            // 6️⃣ Cerrar ventana
            setTimeout(() => window.close(), 800);
        })();
    </script>
</body>
</html>