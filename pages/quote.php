<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Quote - REDY-MED</title>
    <link rel="icon" type="image/png" href="logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
        .quote-section {
            max-width: 800px;
            margin: 4rem auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .quote-section h1 {
            color: #000000;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        
        .quote-section p {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #000000;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .submit-btn {
            background: #00e600;
            color: #000000;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            background: #00ff00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 230, 0, 0.3);
        }
    </style>
</head>
<body>
    <?php include 'include/header.php'; ?>
    
    <section class="quote-section">
        <h1>Get a Quote</h1>
        <p>Fill out the form below and we'll get back to you with a customized quote for your medical equipment needs.</p>
        
        <form action="process_quote.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone">
            </div>
            
            <div class="form-group">
                <label for="company">Company Name</label>
                <input type="text" id="company" name="company">
            </div>
            
            <div class="form-group">
                <label for="product">Product Category</label>
                <select id="product" name="product">
                    <option value="">Select a category</option>
                    <option value="ecg-cables">ECG Cables</option>
                    <option value="spo2-sensors">SpO2 Sensors</option>
                    <option value="nibp-cuffs">NIBP Cuffs</option>
                    <option value="ibp-cables">IBP Cables</option>
                    <option value="temperature-probes">Temperature Probes</option>
                    <option value="batteries">Medical Batteries</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="quantity">Estimated Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1">
            </div>
            
            <div class="form-group">
                <label for="message">Additional Details *</label>
                <textarea id="message" name="message" required placeholder="Please provide details about your requirements..."></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Submit Quote Request</button>
        </form>
    </section>
    
    <?php include 'include/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>

