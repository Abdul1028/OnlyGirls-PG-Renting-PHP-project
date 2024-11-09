<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$listing_id = $_GET['listing_id'] ?? 0;
$listing_title = $_GET['title'] ?? 'this listing';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion - She Shares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fce4ec;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .confirmation-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(255, 128, 171, 0.2);
            text-align: center;
        }
        .icon-container {
            width: 80px;
            height: 80px;
            background: #fff5f8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-container i {
            font-size: 2rem;
            color: #ff4081;
        }
        .btn-delete {
            background: #ff4081;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        .btn-delete:hover {
            opacity: 0.9;
            color: white;
        }
        .btn-keep {
            background: #f8f9fa;
            color: #666;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-keep:hover {
            background: #e9ecef;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="confirmation-card">
                    <div class="icon-container">
                        <i class="fas fa-home"></i>
                    </div>
                    <h4 class="mb-3">Request Accepted!</h4>
                    <p class="text-muted mb-4">
                        Would you like to remove "<?php echo htmlspecialchars($listing_title); ?>" 
                        from your listings now that it's been booked?
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <form action="delete_listing.php" method="POST">
                            <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>">
                            <button type="submit" class="btn btn-delete">
                                <i class="fas fa-trash-alt me-2"></i>Yes, Delete Listing
                            </button>
                        </form>
                        <a href="profile.php?tab=listings&message=kept" class="btn btn-keep">
                            <i class="fas fa-undo me-2"></i>No, Keep Listing
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 