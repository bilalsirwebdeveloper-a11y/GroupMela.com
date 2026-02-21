<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroupMela - Find Best WhatsApp Groups</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* ========== ROOT VARIABLES ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        :root {
            --whatsapp-green: #25D366;
            --whatsapp-dark: #128C7E;
            --whatsapp-teal: #075E54;
            --light-bg: #f0f2f5;
            --white: #ffffff;
            --dark: #1e2a3a;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 5px 20px rgba(0,0,0,0.15);
        }

        body {
            background: var(--light-bg);
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* ========== NAVBAR ========== */
        .navbar {
            background: var(--white);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px 0;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            text-decoration: none;
            color: var(--whatsapp-dark);
        }

        .logo i {
            color: var(--whatsapp-green);
            margin-right: 5px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--whatsapp-green);
        }

        .submit-btn {
            background: var(--whatsapp-green);
            color: var(--white) !important;
            padding: 10px 20px;
            border-radius: 50px;
        }

        .submit-btn:hover {
            background: var(--whatsapp-dark);
            color: var(--white) !important;
        }

        .mobile-menu {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* ========== HERO SECTION ========== */
        .hero {
            background: linear-gradient(135deg, var(--whatsapp-dark), var(--whatsapp-teal));
            color: var(--white);
            padding: 60px 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
            animation: fadeInUp 0.8s;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        /* Search Box */
        .search-box {
            max-width: 600px;
            margin: 0 auto 30px;
            position: relative;
        }

        .search-box form {
            display: flex;
            background: var(--white);
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        .search-box input {
            flex: 1;
            padding: 18px 25px;
            border: none;
            outline: none;
            font-size: 1.1rem;
        }

        .search-box button {
            padding: 0 35px;
            background: var(--whatsapp-green);
            color: var(--white);
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-box button:hover {
            background: var(--whatsapp-dark);
        }

        /* Category Pills */
        .category-pills {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .pill {
            padding: 8px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 30px;
            color: var(--white);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
        }

        .pill:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        /* ========== MAIN CONTENT ========== */
        main {
            padding: 40px 0;
        }

        /* Section Headers */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-header h2 {
            font-size: 2rem;
            color: var(--dark);
        }

        .section-header h2 i {
            color: var(--whatsapp-green);
            margin-right: 10px;
        }

        .view-all {
            color: var(--whatsapp-green);
            text-decoration: none;
            font-weight: 500;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        /* Groups Grid */
        .groups-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .group-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s;
            position: relative;
        }

        .group-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .card-header {
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-badge {
            background: var(--whatsapp-green);
            color: var(--white);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .featured-badge {
            background: gold;
            color: #333;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .new-badge {
            background: #ff4444;
            color: var(--white);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .group-card h3 {
            padding: 15px 15px 5px;
            font-size: 1.2rem;
            color: var(--dark);
        }

        .group-desc {
            padding: 0 15px;
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .group-meta {
            padding: 0 15px 15px;
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
            color: var(--gray);
        }

        .group-meta i {
            margin-right: 4px;
        }

        .card-actions {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .btn-view, .btn-join {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-view {
            background: #f0f0f0;
            color: #333;
        }

        .btn-view:hover {
            background: #e0e0e0;
        }

        .btn-join {
            background: var(--whatsapp-green);
            color: var(--white);
        }

        .btn-join:hover {
            background: var(--whatsapp-dark);
        }

        /* ========== CATEGORIES SECTION ========== */
        .categories-section {
            padding: 40px 0;
            background: var(--white);
            border-radius: 15px;
            margin: 40px 0;
            box-shadow: var(--shadow);
        }

        .categories-section h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.2rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            padding: 0 20px;
        }

        .category-card {
            background: var(--light-bg);
            padding: 25px 15px;
            text-align: center;
            border-radius: 10px;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.3s;
        }

        .category-card:hover {
            transform: translateY(-5px);
            background: var(--whatsapp-green);
            color: var(--white);
        }

        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .category-card h3 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .category-card p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .category-card:hover p {
            color: rgba(255,255,255,0.9);
        }

        /* ========== SUBMIT FORM SECTION ========== */
        .submit-section {
            background: var(--white);
            padding: 50px 0;
            border-radius: 15px;
            margin: 40px 0;
            box-shadow: var(--shadow);
        }

        .submit-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .submit-container h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .submit-container h2 i {
            color: var(--whatsapp-green);
        }

        .subtitle {
            text-align: center;
            color: var(--gray);
            margin-bottom: 30px;
        }

        .submit-form {
            background: var(--light-bg);
            padding: 30px;
            border-radius: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
            background: var(--white);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--whatsapp-green);
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: var(--gray);
            font-size: 0.85rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input {
            width: auto;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: var(--whatsapp-green);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: var(--whatsapp-dark);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .guidelines {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .guidelines h3 {
            margin-bottom: 15px;
            color: var(--dark);
        }

        .guidelines ul {
            list-style: none;
        }

        .guidelines li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }

        .guidelines li:before {
            content: "✓";
            color: var(--whatsapp-green);
            font-weight: bold;
            position: absolute;
            left: 5px;
        }

        /* ========== FOOTER ========== */
        footer {
            background: var(--dark);
            color: var(--white);
            padding: 60px 0 20px;
            margin-top: 60px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h4 {
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .footer-section p {
            color: #adb5bd;
            line-height: 1.8;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: var(--whatsapp-green);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            color: var(--white);
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: var(--whatsapp-green);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #2c3e50;
            color: #adb5bd;
        }

        /* ========== MODAL ========== */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            overflow-y: auto;
        }

        .modal-content {
            background: var(--white);
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            position: relative;
            animation: slideDown 0.3s;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h3 {
            font-size: 1.5rem;
            color: var(--dark);
        }

        .close {
            font-size: 1.8rem;
            cursor: pointer;
            color: var(--gray);
        }

        .close:hover {
            color: var(--dark);
        }

        .group-detail {
            line-height: 1.8;
        }

        .group-detail p {
            margin-bottom: 15px;
        }

        .group-detail strong {
            color: var(--dark);
            min-width: 120px;
            display: inline-block;
        }

        .detail-link {
            color: var(--whatsapp-green);
            word-break: break-all;
        }

        .report-btn {
            background: #dc3545;
            color: var(--white);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .report-btn:hover {
            background: #c82333;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .search-box form {
                flex-direction: column;
                border-radius: 10px;
            }
            
            .search-box button {
                padding: 15px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .groups-grid {
                grid-template-columns: 1fr;
            }
            
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .categories-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                margin: 20px;
                padding: 20px;
            }
        }

        /* Loading Spinner */
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--whatsapp-green);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-content">
            <a href="#" class="logo">
                <i class="fab fa-whatsapp"></i> GroupMela
            </a>
            
            <div class="nav-links">
                <a href="#" onclick="showSection('home')">Home</a>
                <a href="#" onclick="showSection('categories')">Categories</a>
                <a href="#" onclick="showSection('submit')" class="submit-btn">
                    <i class="fas fa-plus-circle"></i> Submit Group
                </a>
            </div>
            
            <div class="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Find Best WhatsApp Groups</h1>
            <p>1000+ Active Groups | Daily Updates | Free to Join</p>
            
            <!-- Search Box -->
            <div class="search-box">
                <form onsubmit="searchGroups(event)">
                    <input type="text" id="searchInput" placeholder="Search groups... (e.g., Cricket, Study, Jobs)">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>
            
            <!-- Category Pills -->
            <div class="category-pills" id="categoryPills">
                <!-- Will be loaded via JavaScript -->
                <span class="loader" style="display: none;"></span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Home Section (Default) -->
            <div id="homeSection">
                <!-- Featured Groups -->
                <section class="featured-groups">
                    <div class="section-header">
                        <h2><i class="fas fa-star"></i> Featured Groups</h2>
                        <a href="#" class="view-all" onclick="showAllFeatured()">View All →</a>
                    </div>
                    
                    <div class="groups-grid" id="featuredGroups">
                        <!-- Will be loaded via JavaScript -->
                        <div class="loader"></div>
                    </div>
                </section>

                <!-- Latest Groups -->
                <section class="latest-groups">
                    <div class="section-header">
                        <h2><i class="fas fa-clock"></i> Latest Groups</h2>
                        <a href="#" class="view-all" onclick="showAllLatest()">View All →</a>
                    </div>
                    
                    <div class="groups-grid" id="latestGroups">
                        <!-- Will be loaded via JavaScript -->
                        <div class="loader"></div>
                    </div>
                </section>
            </div>

            <!-- Categories Section (Hidden by default) -->
            <div id="categoriesSection" style="display: none;">
                <section class="categories-section">
                    <h2><i class="fas fa-th-large"></i> Browse by Categories</h2>
                    
                    <div class="categories-grid" id="allCategories">
                        <!-- Will be loaded via JavaScript -->
                        <div class="loader"></div>
                    </div>
                </section>
            </div>

            <!-- Submit Section (Hidden by default) -->
            <div id="submitSection" style="display: none;">
                <section class="submit-section">
                    <div class="submit-container">
                        <h2><i class="fab fa-whatsapp"></i> Submit Your Group</h2>
                        <p class="subtitle">Get free exposure! Your group will be listed after quick review.</p>
                        
                        <div id="formMessage"></div>
                        
                        <form class="submit-form" id="submitGroupForm" onsubmit="submitGroup(event)">
                            <div class="form-group">
                                <label for="groupName">Group Name *</label>
                                <input type="text" id="groupName" required 
                                       placeholder="e.g., UPSC 2026 Aspirants" maxlength="100">
                            </div>
                            
                            <div class="form-group">
                                <label for="whatsappLink">WhatsApp Group Link *</label>
                                <input type="url" id="whatsappLink" required 
                                       placeholder="https://chat.whatsapp.com/...">
                                <small>Make sure your group link is active</small>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="category">Category *</label>
                                    <select id="category" required>
                                        <option value="">Select Category</option>
                                        <!-- Will be loaded via JavaScript -->
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="language">Language</label>
                                    <select id="language">
                                        <option value="Hindi">Hindi</option>
                                        <option value="English">English</option>
                                        <option value="Hinglish">Hinglish</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description (Optional)</label>
                                <textarea id="description" rows="4" 
                                          placeholder="What is this group about? Rules? Topics?"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Your Email *</label>
                                <input type="email" id="email" required 
                                       placeholder="For verification purposes">
                            </div>
                            
                            <div class="form-group checkbox-group">
                                <input type="checkbox" id="terms" required>
                                <label for="terms">I confirm that this group follows guidelines</label>
                            </div>
                            
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Submit Group
                            </button>
                        </form>
                        
                        <div class="guidelines">
                            <h3>📌 Submission Guidelines</h3>
                            <ul>
                                <li>Link must be a valid WhatsApp group invite link</li>
                                <li>No spam, adult content, or illegal activities</li>
                                <li>Groups should have active members</li>
                                <li>One group per submission</li>
                                <li>Approval usually takes 1-24 hours</li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>GroupMela</h4>
                    <p>India's fastest growing WhatsApp group directory. Find and share WhatsApp groups easily.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-telegram"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#" onclick="showSection('home')">Home</a></li>
                        <li><a href="#" onclick="showSection('categories')">Categories</a></li>
                        <li><a href="#" onclick="showSection('submit')">Submit Group</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Popular Categories</h4>
                    <ul id="footerCategories">
                        <!-- Will be loaded via JavaScript -->
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 GroupMela. All rights reserved. Not affiliated with WhatsApp.</p>
            </div>
        </div>
    </footer>

    <!-- Group Detail Modal -->
    <div id="groupModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Group Details</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div id="groupDetails" class="group-detail">
                <!-- Will be filled via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Report Broken Link</h3>
                <span class="close" onclick="closeReportModal()">&times;</span>
            </div>
            <form onsubmit="submitReport(event)">
                <input type="hidden" id="reportGroupId">
                <div class="form-group">
                    <label for="reportReason">Reason *</label>
                    <select id="reportReason" required>
                        <option value="">Select reason</option>
                        <option value="expired">Link expired / Group full</option>
                        <option value="invalid">Not a WhatsApp link</option>
                        <option value="spam">Spam or inappropriate content</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Submit Report</button>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // ========== SAMPLE DATA (Replace with PHP/Backend) ==========
        const categories = [
            { id: 1, name: 'Study Group', icon: '📚', count: 45 },
            { id: 2, name: 'Business', icon: '💼', count: 32 },
            { id: 3, name: 'Cricket', icon: '🏏', count: 28 },
            { id: 4, name: 'Technology', icon: '💻', count: 41 },
            { id: 5, name: 'Entertainment', icon: '🎬', count: 56 },
            { id: 6, name: 'Jobs Alert', icon: '🔔', count: 23 },
            { id: 7, name: 'Local Groups', icon: '📍', count: 34 },
            { id: 8, name: 'Health & Fitness', icon: '💪', count: 19 }
        ];

        const groups = [
            {
                id: 1,
                name: 'UPSC 2026 Aspirants',
                category: 'Study Group',
                categoryIcon: '📚',
                description: 'Daily current affairs, notes, and discussion for UPSC CSE 2026 aspirants. Join for free study material.',
                members: 1250,
                views: 5432,
                link: 'https://chat.whatsapp.com/example1',
                featured: true,
                language: 'Hindi',
                time: '2 hours ago'
            },
            {
                id: 2,
                name: 'Digital Marketing Experts',
                category: 'Business',
                categoryIcon: '💼',
                description: 'Learn SEO, social media marketing, and make money online. Tips and tricks from experts.',
                members: 890,
                views: 3210,
                link: 'https://chat.whatsapp.com/example2',
                featured: true,
                language: 'English',
                time: '5 hours ago'
            },
            {
                id: 3,
                name: 'IPL 2026 Fan Club',
                category: 'Cricket',
                categoryIcon: '🏏',
                description: 'Live match discussions, team updates, and cricket memes. All teams covered.',
                members: 2100,
                views: 8765,
                link: 'https://chat.whatsapp.com/example3',
                featured: false,
                language: 'Hinglish',
                time: '1 day ago'
            },
            {
                id: 4,
                name: 'Web Development Help',
                category: 'Technology',
                categoryIcon: '💻',
                description: 'HTML, CSS, JavaScript, React. Get help with coding and projects.',
                members: 567,
                views: 1890,
                link: 'https://chat.whatsapp.com/example4',
                featured: true,
                language: 'English',
                time: '3 hours ago'
            },
            {
                id: 5,
                name: 'Bollywood Movies Discussion',
                category: 'Entertainment',
                categoryIcon: '🎬',
                description: 'Latest movie reviews, gossips, and recommendations.',
                members: 3400,
                views: 12345,
                link: 'https://chat.whatsapp.com/example5',
                featured: false,
                language: 'Hindi',
                time: '4 hours ago'
            },
            {
                id: 6,
                name: 'Work From Home Jobs',
                category: 'Jobs Alert',
                categoryIcon: '🔔',
                description: 'Daily work from home job updates. Freelancing and part-time opportunities.',
                members: 1780,
                views: 6543,
                link: 'https://chat.whatsapp.com/example6',
                featured: true,
                language: 'English',
                time: '6 hours ago'
            },
            {
                id: 7,
                name: 'Delhi NCR Deals',
                category: 'Local Groups',
                categoryIcon: '📍',
                description: 'Best deals, offers, and second-hand items in Delhi NCR.',
                members: 890,
                views: 2345,
                link: 'https://chat.whatsapp.com/example7',
                featured: false,
                language: 'Hindi',
                time: '12 hours ago'
            },
            {
                id: 8,
                name: 'Yoga & Meditation',
                category: 'Health & Fitness',
                categoryIcon: '💪',
                description: 'Daily yoga sessions, meditation tips, and healthy lifestyle.',
                members: 456,
                views: 1234,
                link: 'https://chat.whatsapp.com/example8',
                featured: false,
                language: 'English',
                time: '1 day ago'
            }
        ];

        // ========== INITIAL LOAD ==========
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            loadCategoryPills();
            loadFeaturedGroups();
            loadLatestGroups();
            loadFooterCategories();
            populateCategoryDropdown();
        });

        // ========== LOAD CATEGORIES ==========
        function loadCategories() {
            let html = '';
            categories.forEach(cat => {
                html += `
                    <a href="#" class="category-card" onclick="showCategory(${cat.id})">
                        <div class="category-icon">${cat.icon}</div>
                        <h3>${cat.name}</h3>
                        <p>${cat.count} Groups</p>
                    </a>
                `;
            });
            document.getElementById('allCategories').innerHTML = html;
        }

        // ========== LOAD CATEGORY PILLS ==========
        function loadCategoryPills() {
            let html = '';
            categories.slice(0, 8).forEach(cat => {
                html += `<a href="#" class="pill" onclick="showCategory(${cat.id})">${cat.icon} ${cat.name}</a>`;
            });
            document.getElementById('categoryPills').innerHTML = html;
        }

        // ========== LOAD FEATURED GROUPS ==========
        function loadFeaturedGroups() {
            let html = '';
            const featured = groups.filter(g => g.featured).slice(0, 4);
            
            featured.forEach(group => {
                html += createGroupCard(group);
            });
            
            if (featured.length === 0) {
                html = '<p style="grid-column: 1/-1; text-align: center;">No featured groups</p>';
            }
            
            document.getElementById('featuredGroups').innerHTML = html;
        }

        // ========== LOAD LATEST GROUPS ==========
        function loadLatestGroups() {
            let html = '';
            const latest