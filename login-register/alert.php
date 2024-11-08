<?php
function showAlert($title, $message, $redirectUrl) {
    ?>
    <div class="alert-overlay">
        <div class="custom-alert">
            <i class="fas fa-check-circle mb-3" style="font-size: 3rem; color: #ff4081;"></i>
            <h4><?php echo $title; ?></h4>
            <p><?php echo $message; ?></p>
            <div class="progress" style="height: 3px; width: 100%;">
                <div class="progress-bar" role="progressbar" style="width: 0%; background-color: #ff4081;"></div>
            </div>
        </div>
    </div>

    <style>
        .alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }

        .custom-alert {
            background: #F8BBD0;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-out;
            max-width: 90%;
            width: 400px;
        }

        .custom-alert h4 {
            color: #d81b60;
            margin-bottom: 0.5rem;
        }

        .custom-alert p {
            color: #333;
            margin-bottom: 1rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.5);
        }
    </style>

    <script>
        let progress = document.querySelector('.progress-bar');
        let width = 0;
        let interval = setInterval(() => {
            width += 1;
            progress.style.width = width + '%';
            if (width >= 100) {
                clearInterval(interval);
                window.location.href = '<?php echo $redirectUrl; ?>';
            }
        }, 20); // Will take 2 seconds to complete
    </script>
    <?php
}

function showErrorAlert($title, $message) {
    ?>
    <div class="alert-overlay">
        <div class="custom-alert" style="background: #ffebee;">
            <i class="fas fa-exclamation-circle mb-3" style="font-size: 3rem; color: #c62828;"></i>
            <h4 style="color: #c62828;"><?php echo $title; ?></h4>
            <p><?php echo $message; ?></p>
            <button onclick="closeAlert()" class="btn" 
                    style="background-color: #c62828; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
                Close
            </button>
        </div>
    </div>

    <script>
        function closeAlert() {
            document.querySelector('.alert-overlay').remove();
        }
    </script>
    <?php
}
?> 