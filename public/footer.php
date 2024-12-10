<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Ensure footer sticks to the bottom of the page */
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .footer-container {
            margin-top: auto;
        }

        /* Remove the underline from the Developed by link */
        .footer-container a {
            text-decoration: none;
        }
    </style>
</head>

<body style="background-color: #212529; color: white;">

    <!-- Your Page Content Here -->

    <!-- Footer Section -->
    <footer class="py-2 footer-container" style="background-color: #000; color: white;">
        <div class="container">
            <div class="row align-items-center justify-content-between mt-1">
                <!-- Copyright -->
                <div class="col-12 col-md-6 text-center text-md-start">
                    <p>&copy; <span id="current-year"></span> | Ritzy Technology</p>
                </div>

                <!-- Developed By Link -->
                <div class="col-12 col-md-6 text-center text-md-end">
                    <p>Developed by <a href="https://github.com/tushar868" class="text-white" target="_blank">Tushar</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script to automatically update the current year -->
    <script>
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
