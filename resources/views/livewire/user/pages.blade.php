<div>
    <style>
        :root {
            --primary: #66ccff;
            --secondary: #0A2B4E;
            --accent: #F59E0B;
            --gray-bg: #F8FAFC;
            --gray-text: #475569;
            --border: #E2E8F0;
        }
        
        .page-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px 0 40px;
        }
        
        .page-card {
            background: white;
            border-radius: 24px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        
        .page-header {
            background: var(--secondary);
            padding: 24px 28px;
            border-bottom: 3px solid var(--primary);
        }
        
        .page-header h1 {
            color: white;
            font-size: 26px;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-header h1 i {
            color: var(--primary);
            font-size: 28px;
        }
        
        .page-body {
            padding: 32px 36px;
        }
        
        .page-content {
            font-size: 16px;
            line-height: 1.8;
            color: var(--gray-text);
            text-align: justify;
        }
        
        .page-content h2 {
            font-size: 22px;
            color: var(--secondary);
            margin: 24px 0 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        
        .page-content h3 {
            font-size: 18px;
            color: var(--secondary);
            margin: 20px 0 12px;
        }
        
        .page-content p {
            margin-bottom: 16px;
        }
        
        .page-content ul, .page-content ol {
            margin: 16px 0;
            padding-right: 24px;
        }
        
        .page-content li {
            margin-bottom: 8px;
        }
        
        .page-content a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .page-content a:hover {
            color: var(--accent);
        }
        
        .page-content strong {
            color: var(--secondary);
        }
        
        .page-content img {
            max-width: 100%;
            border-radius: 16px;
            margin: 20px 0;
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 12px;
            }
            
            .page-header {
                padding: 18px 20px;
            }
            
            .page-header h1 {
                font-size: 20px;
            }
            
            .page-header h1 i {
                font-size: 22px;
            }
            
            .page-body {
                padding: 20px 24px;
            }
            
            .page-content {
                font-size: 14px;
            }
        }
    </style>

    <div class="page-container">
        <div class="page-card">
            <div class="page-header">
                <h1>
                    <i class="fa fa-file-text-o"></i>
                    {{ $page->title }}
                </h1>
            </div>
            <div class="page-body">
                <div class="page-content">
                    @php echo $page->text; @endphp
                </div>
            </div>
        </div>
    </div>
</div>