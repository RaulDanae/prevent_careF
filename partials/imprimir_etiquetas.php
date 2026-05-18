<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimiendo Etiquetas</title>

    <?php require_once __DIR__ . '/../config/config1.php'; ?>
    <!-- Text2Barcode (OFICIAL) -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
</head>

<body>
    <p>Imprimiendo Etiquetas</p>

    <script>

    // Convertir a codigo de BArras
        JsBarcode("#barcode_1", "EV0000000140", {
            format: "CODE128",
            width: 2,
            height: 40,
            displayValue: false
        });


    // IMprimimr
        function generarEtiquetas(data) {

            let html = '';

            data.forEach((e, index) => {

                html += `
                <div class="etiqueta">
                    <svg id="barcode_${index}"></svg>

                    <div class="nombre">${e.nombre}</div>

                    <div>${e.edad} ${e.genero}</div>

                    <div class="recipiente">${e.recipiente}</div>

                    <div class="footer">
                        ${e.fecha} ${e.hora}
                    </div>
                </div>
                `;
            });

            return html;
        }

        // Para ver vista previa
        function imprimirEtiquetas(data) {

            let ventana = window.open('', '_blank');

            let contenido = `
            <html>
            <head>
                <title>Etiquetas</title>
                <style>
                    body {
                        margin: 0;
                        display: flex;
                        flex-wrap: wrap;
                        gap: 5px;
                    }

                    ${document.querySelector('style').innerHTML}
                </style>
            </head>
            <body>
                ${generarEtiquetas(data)}
            </body>
            </html>
            `;

            ventana.document.write(contenido);
            ventana.document.close();

            // 🔥 Esperar render
            setTimeout(() => {

                data.forEach((e, index) => {
                    JsBarcode(`#barcode_${index}`, e.id, {
                        format: "CODE128",
                        width: 2,
                        height: 40,
                        displayValue: false
                    });
                });

            }, 300);

        }

    </script>
</body>
</html>