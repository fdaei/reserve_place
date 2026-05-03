<div>
    
    @push('head')
        @php
            $residenceType = \App\Models\Residence::getResidenceType()[$residence->residence_type] ?? 'اقامتگاه';
            $areaType = \App\Models\Residence::getAreaType()[$residence->area_type] ?? '';
        @endphp
        <title>{{ $residence->title }} | اجاره {{ $residenceType }} در {{ $residence->city->name }} | اینجا</title>
        <meta name="description" content="{{ Str::limit($residence->title . ' - ' . $residenceType . ' ' . $areaType . ' در ' . $residence->city->name . '، استان ' . $residence->province->name . ' با ' . $residence->room_number . ' اتاق و ظرفیت ' . $residence->people_number . ' نفر', 155) }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta property="og:title" content="{{ $residence->title }}">
        <meta property="og:image" content="{{ asset('storage/residences/' . $residence->image) }}">
    @endpush

    <style>
        :root {
            --primary: #66ccff;
            --secondary: #0A2B4E;
            --accent: #F59E0B;
            --gray-bg: #F8FAFC;
            --gray-text: #475569;
            --border: #E2E8F0;
        }
        
        .breadcrumb-section {
            margin: 15px 0 20px;
        }
        
        .breadcrumb-list {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            background: white;
            padding: 12px 20px;
            border-radius: 60px;
            border: 1px solid var(--border);
        }
        
        .breadcrumb-list a {
            color: var(--primary);
            text-decoration: none;
            font-size: 13px;
        }
        
        .detail-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .detail-code-box {
            background: var(--gray-bg);
            padding: 6px 16px;
            border-radius: 40px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            border: 1px solid var(--border);
            font-size: 14px;
        }
        
        .detail-code-box i {
            color: var(--primary);
        }
        
        .detail-location {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        
        .location-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .location-info i {
            color: var(--primary);
        }
        
        .share-btn {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 8px 20px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .share-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .gallery-container {
            margin-bottom: 24px;
        }
        
        .main-gallery-img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            border-radius: 24px;
        }
        
        .gallery-thumbs {
            display: flex;
            gap: 10px;
            margin-top: 12px;
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: thin;
        }
        
        .thumb-item {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
        }
        
        .thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .thumb-item:hover {
            transform: scale(1.05);
        }
        
        .thumb-active {
            border-color: var(--primary);
        }
        
        .gallery-thumbs::-webkit-scrollbar {
            height: 4px;
        }
        
        .gallery-thumbs::-webkit-scrollbar-track {
            background: var(--border);
            border-radius: 10px;
        }
        
        .gallery-thumbs::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
        
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }
        
        .info-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            border: 1px solid var(--border);
        }
        
        .type-badge {
            background: var(--secondary);
            color: white;
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .price-large {
            font-size: 26px;
            font-weight: 800;
            color: var(--secondary);
        }
        
        .info-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-row i {
            width: 24px;
            color: var(--primary);
        }
        
        .btn-call {
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 12px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            text-align: center;
            margin-top: 16px;
            transition: all 0.2s;
        }
        
        .btn-call:hover {
            background: #D97706;
            transform: translateY(-2px);
        }
        
        .host-description {
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 24px;
            border-right: 4px solid var(--primary);
        }
        
        .features-section {
            margin-bottom: 24px;
        }
        
        .features-category {
            margin-bottom: 20px;
        }
        
        .features-category h4 {
            font-size: 16px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }
        
        .feature-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: var(--gray-bg);
            border-radius: 40px;
            font-size: 13px;
            transition: all 0.2s;
        }
        
        .feature-chip.disabled {
            opacity: 0.6;
            background: #F1F5F9;
        }
        
        .feature-chip.disabled .text-muted {
            color: #94A3B8;
        }
        
        .feature-chip i {
            font-size: 14px;
        }
        
        .map-container {
            margin-bottom: 24px;
        }
        
        .map-container #map {
            height: 350px;
            border-radius: 24px;
            overflow: hidden;
        }
        
        .rules-container {
            background: #FEF3C7;
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .rules-container h3 {
            color: #92400E;
            margin-bottom: 12px;
        }
        
        .rules-container ul {
            padding-right: 20px;
            color: #92400E;
        }
        
        .similar-section {
            margin: 32px 0;
        }
        
        .similar-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 20px;
        }
        
        .similar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }
        
        .similar-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        
        .similar-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -12px rgba(0,0,0,0.1);
        }
        
        .similar-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
        
        .similar-card-content {
            padding: 12px;
        }
        
        .similar-card-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--secondary);
        }
        
        .similar-price {
            font-size: 16px;
            font-weight: 800;
            color: var(--secondary);
        }
        
        .similar-view-btn {
            background: var(--accent);
            color: white;
            padding: 6px 16px;
            border-radius: 40px;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
            transition: all 0.2s;
        }
        
        .similar-view-btn:hover {
            background: #D97706;
        }
        
        @media (max-width: 768px) {
            .two-columns {
                grid-template-columns: 1fr;
            }
            .main-gallery-img {
                height: 250px;
            }
            .detail-title {
                font-size: 20px;
            }
            .thumb-item {
                width: 60px;
                height: 60px;
            }
        }
        
        /* بخش نظرات */
        .reviews-section {
            margin: 32px 0;
            background: white;
            border-radius: 28px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        
        .reviews-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, #F8FAFE 0%, #FFFFFF 100%);
            border-bottom: 1px solid var(--border);
        }
        
        .reviews-summary {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .summary-rating {
            display: flex;
            align-items: baseline;
            gap: 4px;
            background: var(--secondary);
            padding: 8px 20px;
            border-radius: 60px;
        }
        
        .rating-number {
            font-size: 28px;
            font-weight: 800;
            color: white;
        }
        
        .rating-out {
            font-size: 13px;
            color: rgba(255,255,255,0.8);
        }
        
        .summary-stars i {
            color: #F59E0B;
            font-size: 18px;
            margin: 0 2px;
        }
        
        .summary-count {
            color: var(--gray-text);
            font-size: 14px;
        }
        
        .review-form-card {
            margin: 24px 28px;
            background: var(--gray-bg);
            border-radius: 24px;
            overflow: hidden;
        }
        
        .form-header {
            background: var(--secondary);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .form-header i {
            color: var(--primary);
            font-size: 20px;
        }
        
        .form-header h4 {
            color: white;
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .form-body {
            padding: 24px;
        }
        
        .rating-input-group {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .rating-label {
            font-weight: 600;
            color: var(--secondary);
        }
        
        .stars-input {
            display: flex;
            flex-direction: row-reverse;
            gap: 8px;
        }
        
        .stars-input input {
            display: none;
        }
        
        .stars-input label {
            font-size: 28px;
            color: #CBD5E0;
            cursor: pointer;
            transition: 0.2s;
        }
        
        .stars-input label:hover,
        .stars-input label:hover ~ label,
        .stars-input input:checked ~ label {
            color: #F59E0B;
        }
        
        .comment-input-group textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 14px;
            font-family: inherit;
            font-size: 13px;
            resize: vertical;
            transition: 0.2s;
        }
        
        .comment-input-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102,204,255,0.1);
        }
        
        .btn-submit-review {
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 12px 28px;
            font-weight: 600;
            margin-top: 16px;
            cursor: pointer;
            transition: 0.2s;
        }
        
        .btn-submit-review:hover {
            background: #D97706;
            transform: translateY(-2px);
        }
        
        .review-alert {
            margin: 24px 28px;
            padding: 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }
        
        .review-alert i {
            font-size: 24px;
        }
        
        .already-reviewed {
            background: #E8F5E9;
            color: #2E7D32;
        }
        
        .already-reviewed i {
            color: #4CAF50;
        }
        
        .login-required {
            background: #FFF3E0;
            color: #E65100;
        }
        
        .login-required i {
            color: #FF9800;
        }
        
        .login-required a {
            color: #E65100;
            font-weight: 700;
        }
        
        .reviews-list {
            padding: 0 28px 28px;
        }
        
        .list-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 0;
            border-bottom: 2px solid var(--border);
            margin-bottom: 16px;
        }
        
        .list-header i {
            color: var(--primary);
            font-size: 18px;
        }
        
        .list-header span {
            font-weight: 700;
            color: var(--secondary);
        }
        
        .review-card {
            display: flex;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid var(--border);
        }
        
        .review-sidebar {
            flex-shrink: 0;
            width: 100px;
            text-align: center;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            margin: 0 auto 8px;
            background: var(--gray-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .user-avatar i {
            font-size: 36px;
            color: var(--primary);
        }
        
        .user-name {
            font-weight: 600;
            color: var(--secondary);
            font-size: 13px;
        }
        
        .review-date {
            font-size: 11px;
            color: var(--gray-text);
            margin-top: 4px;
        }
        
        .review-content {
            flex: 1;
        }
        
        .review-stars i {
            color: #F59E0B;
            font-size: 14px;
            margin: 0 1px;
        }
        
        .review-text {
            margin-top: 8px;
            font-size: 14px;
            line-height: 1.6;
            color: var(--gray-text);
        }
        
        .review-empty {
            text-align: center;
            padding: 48px 28px;
            color: var(--gray-text);
        }
        
        .review-empty i {
            font-size: 48px;
            color: var(--border);
            margin-bottom: 12px;
            display: block;
        }
        
        @media (max-width: 768px) {
            .reviews-header, .review-form-card, .review-alert {
                margin: 16px;
            }
            .reviews-list {
                padding: 0 16px 16px;
            }
            .review-card {
                flex-direction: column;
                gap: 12px;
            }
            .review-sidebar {
                width: 100%;
                display: flex;
                align-items: center;
                gap: 12px;
                text-align: left;
            }
            .user-avatar {
                width: 45px;
                height: 45px;
                margin: 0;
            }
            .user-avatar i {
                font-size: 28px;
            }
            .stars-input label {
                font-size: 24px;
            }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes starPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        
        .review-card {
            animation: fadeInUp 0.4s ease-out backwards;
        }
        
        .animated-star {
            animation: starPulse 0.5s ease-in-out;
        }
        
        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .write-review-btn {
            background: linear-gradient(135deg, var(--accent) 0%, #D97706 100%);
            border: none;
            padding: 10px 24px;
            border-radius: 40px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(245,158,11,0.3);
        }
        
        .write-review-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245,158,11,0.4);
        }
        
        .review-form-card {
            transition: all 0.3s ease;
        }
        
        .rating-feedback {
            font-size: 13px;
            padding: 4px 12px;
            background: white;
            border-radius: 40px;
            color: var(--secondary);
            font-weight: 500;
        }
        
        .char-counter {
            text-align: left;
            font-size: 11px;
            color: var(--gray-text);
            margin-top: 4px;
        }
        
        .comment-input-group {
            position: relative;
        }
        
        .review-footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed var(--border);
        }
        
        .like-btn {
            background: transparent;
            border: none;
            color: var(--gray-text);
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        
        .like-btn:hover {
            color: var(--primary);
        }
        
        .like-btn i {
            font-size: 14px;
        }
        
        [x-cloak] {
            display: none !important;
        }
        
        .review-card {
            transition: all 0.3s;
        }
        
        .review-card:hover {
            background: linear-gradient(135deg, rgba(102,204,255,0.02) 0%, rgba(102,204,255,0.05) 100%);
            transform: translateX(4px);
        }
        
        .btn-submit-review:disabled {
            opacity: 0.7;
            cursor: wait;
        }
        
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.95);
            z-index: 9999;
            cursor: pointer;
        }
        
        .lightbox.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 40px;
            color: white;
            cursor: pointer;
            z-index: 10000;
            font-weight: bold;
        }
        
        .lightbox-content {
            position: relative;
            width: 90%;
            max-width: 1200px;
            margin: auto;
        }
        
        .lightbox-content img {
            width: 100%;
            height: auto;
            max-height: 85vh;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .lightbox-prev, .lightbox-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            font-size: 30px;
            padding: 16px 20px;
            cursor: pointer;
            border-radius: 50%;
            transition: 0.2s;
        }
        
        .lightbox-prev:hover, .lightbox-next:hover {
            background: rgba(255,255,255,0.4);
        }
        
        .lightbox-prev {
            left: -50px;
        }
        
        .lightbox-next {
            right: -50px;
        }
        
        .lightbox-counter {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            background: rgba(0,0,0,0.6);
            padding: 6px 12px;
            border-radius: 40px;
            font-size: 14px;
        }
        
        .main-image-wrapper {
            position: relative;
            cursor: pointer;
        }
        
        .image-overlay {
            position: absolute;
            bottom: 16px;
            right: 16px;
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 6px 12px;
            border-radius: 40px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            opacity: 0;
            transition: opacity 0.2s;
        }
        
        .main-image-wrapper:hover .image-overlay {
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .lightbox-prev {
                left: 10px;
            }
            .lightbox-next {
                right: 10px;
            }
            .lightbox-prev, .lightbox-next {
                font-size: 24px;
                padding: 12px 16px;
            }
        }
        
        .map-container {
            margin-bottom: 24px;
        }
        
        .map-card {
            background: white;
            border-radius: 24px;
            border: 1px solid var(--border);
            padding: 28px 24px;
            text-align: center;
        }
        
        .map-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--secondary) 0%, #1A4A6E 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        
        .map-icon i {
            font-size: 32px;
            color: var(--primary);
        }
        
        .map-card h4 {
            font-size: 18px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .map-card p {
            color: var(--gray-text);
            font-size: 14px;
            margin-bottom: 4px;
        }
        
        .map-address {
            font-size: 12px;
            color: var(--primary);
            margin-top: 8px;
            margin-bottom: 16px !important;
        }
        
        .map-btn {
            display: inline-block;
            background: var(--accent);
            color: white;
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            margin-top: 8px;
        }
        
        .map-btn:hover {
            background: #D97706;
            transform: translateY(-2px);
        }
        
        .map-placeholder {
            height: 250px;
            border-radius: 24px;
            background: var(--gray-bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--gray-text);
        }
        
        .map-placeholder i {
            font-size: 48px;
            margin-bottom: 12px;
            color: var(--border);
        }
        
        /* بخش انتخاب تاریخ */
        .booking-dates {
            margin-bottom: 24px;
        }
        
        .date-range-container {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .date-input-group {
            flex: 1;
            min-width: 200px;
        }
        
        .date-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .date-label i {
            color: var(--primary);
            margin-left: 6px;
        }
        
        .date-input-wrapper {
            position: relative;
        }
        
        .date-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 16px;
            font-size: 14px;
            font-family: inherit;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }
        
        .date-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102,204,255,0.1);
        }
        
        .date-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            pointer-events: none;
        }
        
        .date-divider {
            color: var(--gray-text);
            font-size: 20px;
        }
        
        .nights-count {
            text-align: center;
            padding: 12px;
            background: var(--gray-bg);
            border-radius: 40px;
            margin-bottom: 16px;
            font-size: 14px;
            color: var(--secondary);
        }
        
        .nights-count i {
            color: var(--primary);
            margin-left: 6px;
        }
        
        
        .price-breakdown {
            background: var(--gray-bg);
            border-radius: 20px;
            padding: 16px;
            margin-top: 8px;
        }
        
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }
        
        .breakdown-total {
            display: flex;
            justify-content: space-between;
            padding: 12px 0 0;
            margin-top: 8px;
            font-size: 16px;
            color: var(--secondary);
        }
        
        @media (max-width: 768px) {
            .date-range-container {
                flex-direction: column;
            }
            .date-divider {
                transform: rotate(90deg);
            }
            .date-input-group {
                width: 100%;
            }
        }
        
        /* اضافه کنید به انتهای استایل‌های صفحه detail */
.header-wrapper .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-size: cover;
    background-position: center;
    margin: 0;
    display: block;
}
    </style>

    @php
        $residenceType = \App\Models\Residence::getResidenceType()[$residence->residence_type] ?? 'اقامتگاه';
        $areaType = \App\Models\Residence::getAreaType()[$residence->area_type] ?? '';
        $options = $residence->optionValues->keyBy("option_id");
        $bookingMode = getConfigs('booking_mode') ?? 'call';
        $phoneNumber = preg_replace('/[^0-9]/', '', \App\Models\User::find($residence->user_id)->phone ?? '');
        $defaultImage = asset('storage/static/onerror.jpg');
        
        $allImages = [];
        $mainImageUrl = asset('storage/residences/' . $residence->image);
        if (!file_exists(public_path('storage/residences/' . $residence->image))) {
            $mainImageUrl = $defaultImage;
        }
        $allImages[] = $mainImageUrl;
        
        foreach($residence->images as $image) {
            if($residence->image != $image->url) {
                $imgPath = asset('storage/residences/' . $image->url);
                if (!file_exists(public_path('storage/residences/' . $image->url))) {
                    $imgPath = $defaultImage;
                }
                $allImages[] = $imgPath;
            }
        }
        
        $allImagesJson = json_encode($allImages);
        
        $dynamicDescriptionDisplay = $residence->title . ' در ' . $residence->city->name . '، ' . $residence->province->name;
        $dynamicDescriptionDisplay .= ' - ' . $residenceType . ' ' . $areaType;
        if ($residence->room_number > 0) {
            $dynamicDescriptionDisplay .= ' با ' . $residence->room_number . ' اتاق خواب';
        }
        if ($residence->people_number > 0) {
            $dynamicDescriptionDisplay .= ' و ظرفیت ' . $residence->people_number . ' نفر';
        }
    @endphp

    {{-- مسیریاب --}}
    <div class="breadcrumb-section">
        <div class="breadcrumb-list">
            <a href="{{ url('/') }}">صفحه اصلی</a>
            <span>/</span>
            <a href="{{ url('/') }}">اقامتگاه‌ها</a>
            <span>/</span>
            <span>{{ Str::limit($residence->title, 40) }}</span>
        </div>
    </div>

    {{-- عنوان + کد اقامتگاه --}}
    <h1 class="detail-title">{{ $residence->title }}</h1>
    <div class="detail-code-box">
        <i class="fa fa-barcode"></i> کد اقامتگاه: 
        <strong style="font-size: 18px; letter-spacing: 1px;">{{ $residence->id }}</strong>
    </div>

    <div class="detail-location">
        <div class="location-info">
            <i class="fa fa-map-marker"></i>
            <span>استان {{ $residence->province->name }}، {{ $residence->city->name }}</span>
        </div>
        <button class="share-btn" id="shareBtn">
            <i class="fa fa-share-alt"></i> اشتراک گذاری
        </button>
    </div>

    {{-- گالری با لایت‌باکس --}}
    <div class="gallery-container">
        <div class="main-image-wrapper" onclick="openLightbox({{ $allImagesJson }}, 0)">
            <img id="mainGalleryImg" class="main-gallery-img" src="{{ $allImages[0] }}" alt="{{ $residence->title }}">
            <div class="image-overlay">
                <i class="fa fa-expand"></i>
                <span>مشاهده بزرگ</span>
            </div>
        </div>
        
        @if(count($allImages) > 1)
        <div class="gallery-thumbs">
            @foreach($allImages as $index => $imgPath)
                <div class="thumb-item {{ $index == 0 ? 'thumb-active' : '' }}" onclick="event.stopPropagation(); changeGalleryImageQuick('{{ $imgPath }}', this, {{ $index }})">
                    <img src="{{ $imgPath }}" alt="تصویر {{ $index + 1 }}">
                </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- لایت‌باکس --}}
    <div id="lightbox" class="lightbox" onclick="closeLightbox()">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <div class="lightbox-content">
            <img id="lightboxImg" src="">
            <button class="lightbox-prev" onclick="event.stopPropagation(); changeLightboxImage(-1)">❮</button>
            <button class="lightbox-next" onclick="event.stopPropagation(); changeLightboxImage(1)">❯</button>
        </div>
        <div class="lightbox-counter" id="lightboxCounter"></div>
    </div>

    {{-- دو ستونه --}}
    <div class="two-columns">
        <div class="info-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <span class="type-badge">{{ $residenceType }} {{ $areaType }}</span>
                <div>
                    <span class="price-large">{{ number_format($residence->amount) }}</span>
                    <span style="color: var(--gray-text); font-size: 13px;">تومان/شب</span>
                </div>
            </div>
            
            <div class="info-row">
    @php
        $host = $residence->admin;
        $hasAvatar = $host && $host->profile_image && $host->profile_image != '';
        $hasName = $host->name && $host->family;
    @endphp
    
    @if($hasAvatar)
        <img src="{{ asset('storage/user/' . $host->profile_image) }}" 
             style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; margin-left: 8px;">
    @else
        <i class="fa fa-user-circle-o" style="font-size: 20px;"></i>
    @endif
    
    @if($hasName)
        <span>{{ $host->name }} {{ $host->family }}</span>
    @elseif($host->name)
        <span>{{ $host->name }}</span>
    @else
        <span style="font-size: 12px; color: #666;">آقا/خانم میزبان</span>
    @endif
</div>
            
<div class="info-row">
    @if(!auth()->check())
        <i class="fa fa-lock" style="color: var(--accent); width: 24px;"></i>
        <a href="{{ url('login') }}" style="color: var(--accent); cursor: pointer; text-decoration: none;">
            برای مشاهده شماره تماس وارد شوید
        </a>
    @else
        <i class="fa fa-phone" style="color: var(--accent); width: 24px;"></i>
        <span style="direction: ltr; color: var(--accent); font-weight: 600;">
            {{ $phoneNumber }}
        </span>
    @endif
</div>


            
            <div class="info-row">
                <i class="fa fa-users"></i>
                <span>ظرفیت: {{ $residence->people_number }} نفر</span>
            </div>
            
            <div class="info-row">
                <i class="fa fa-bed"></i>
                <span>{{ $residence->room_number }} اتاق خواب</span>
            </div>
            
            <div class="info-row">
                <i class="fa fa-expand"></i>
                <span>{{ $residence->area }} متر مربع</span>
            </div>
            
            <div class="info-row">
                <i class="fa fa-calendar"></i>
                <span>قیمت آخر هفته: {{ number_format($residence->last_week_amount) }} تومان/شب</span>
            </div>
            
            @if($residence->point > 0)
                <div class="info-row">
                    <i class="fa fa-star" style="color: #F59E0B;"></i>
                    <span>امتیاز: {{ number_format($residence->point, 1) }} از 5 ({{ $residence->comments->count() }} نظر)</span>
                </div>
            @endif
        </div>
        
        <div class="info-card">
            <div class="info-row">
                <i class="fa fa-map-marker"></i>
                <span>{{ $residence->address ?? 'آدرس دقیق در تماس اعلام می‌شود' }}</span>
            </div>
            
            <div class="info-row">
                <i class="fa fa-clock-o"></i>
                <span>ساعت تحویل: 14:00 | ساعت تخلیه: 12:00</span>
            </div>
            
            {{-- انتخاب تاریخ رزرو --}}
            <div class="booking-dates">
                <h4 style="margin: 16px 0 12px; font-size: 15px; color: var(--secondary);">
    <i class="fa fa-calendar" style="color: var(--primary); margin-left: 8px;"></i> 
    تاریخ اقامت
</h4>
                
                <div class="date-range-container">
                    <div class="date-input-group">
                        <label class="date-label">تاریخ ورود</label>
                        <div class="date-input-wrapper">
                            <input type="text" id="checkin_date" class="date-input" placeholder="انتخاب تاریخ" readonly>
                            <i class="fa fa-calendar-check-o date-icon"></i>
                        </div>
                    </div>
                    <div class="date-divider">←</div>
                    <div class="date-input-group">
                        <label class="date-label">تاریخ خروج</label>
                        <div class="date-input-wrapper">
                            <input type="text" id="checkout_date" class="date-input" placeholder="انتخاب تاریخ" readonly>
                            <i class="fa fa-calendar-check-o date-icon"></i>
                        </div>
                    </div>
                </div>
                
                <div id="nightsInfo" class="nights-count" style="display: none;">
                    <i class="fa fa-moon-o"></i> <span id="nightsCount">0</span> شب اقامت
                </div>
                
                <div id="priceDetail" class="price-breakdown" style="display: none;">
                    <div class="breakdown-item">
                        <span>{{ number_format($residence->amount) }} تومان × <span id="priceNights">0</span> شب</span>
                        <span id="totalPrice">{{ number_format($residence->amount) }} تومان</span>
                    </div>
                    <div class="breakdown-total">
                        <strong>قیمت نهایی:</strong>
                        <strong id="finalTotal">{{ number_format($residence->amount) }} تومان</strong>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 16px;">
                @if($bookingMode == 'call')
                    <a href="https://wa.me/{{ $phoneNumber }}" class="btn-call" style="background: #25D366; display: block; text-decoration: none;" target="_blank">
                        <i class="fa fa-whatsapp"></i> رزرو از طریق واتساپ
                    </a>
                @else
                    <button class="btn-call" id="reserveRequestBtn" wire:click="submitReservationRequest">
                        <i class="fa fa-calendar-check-o"></i> ثبت درخواست رزرو
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- توضیحات میزبان --}}
    <div class="host-description">
        <h3><i class="fa fa-info-circle" style="color: var(--primary);"></i> توضیحات میزبان</h3>
        <p style="line-height: 1.7; color: var(--gray-text); text-align: justify;">
            {{ $dynamicDescriptionDisplay }}
        </p>
    </div>

    {{-- امکانات --}}
    <div class="features-section info-card">
        <h3 style="margin-bottom: 20px; color: var(--secondary);">
            <i class="fa fa-check-circle" style="color: var(--primary);"></i> امکانات اقامتگاه
        </h3>
        @foreach(\App\Models\OptionCategory::where("type","residence")->get() as $category)
            <div class="features-category">
                <h4>{{ $category->title }}</h4>
                <div class="features-grid">
                    @foreach($category->options as $option)
                        @php $hasOption = isset($options[$option->id]); @endphp
                        <div class="feature-chip {{ !$hasOption ? 'disabled' : '' }}">
                            @if($hasOption)
                                <i class="fa fa-check-circle" style="color: var(--primary);"></i>
                            @else
                                <i class="fa fa-times-circle" style="color: #EF4444;"></i>
                            @endif
                            <span class="{{ !$hasOption ? 'text-muted' : '' }}">{{ $option->title }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- نقشه --}}
    <div class="map-container">
        <div id="map"></div>
    </div>

    {{-- قوانین --}}
    <div class="rules-container">
        <h3><i class="fa fa-gavel"></i> قوانین عمومی</h3>
        <ul>
            <li>همراه داشتن کارت ملی الزامی است.</li>
            <li>تغییر ساعت تحویل تنها با هماهنگی قبلی ممکن است.</li>
            <li>حضور مهمانان اضافی ممنوع و مشمول جریمه خواهد بود.</li>
            <li>در صورت آسیب به وسایل، هزینه تعمیر از کاربر دریافت می‌شود.</li>
        </ul>
    </div>
    
    {{-- نظرات و امتیازات --}}
    <div class="reviews-section" x-data="{ showForm: false }">
        <div class="reviews-header">
            <div class="reviews-summary">
                <div class="summary-rating">
                    <span class="rating-number">{{ number_format($residence->point, 1) }}</span>
                    <span class="rating-out">از 5</span>
                </div>
                <div class="summary-stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($residence->point))
                            <i class="fa fa-star animated-star"></i>
                        @else
                            <i class="fa fa-star-o"></i>
                        @endif
                    @endfor
                </div>
                <div class="summary-count">
                    <i class="fa fa-comments"></i> {{ $residence->comments->count() }} نظر
                </div>
            </div>
            
            @auth
                @php $userComment = \App\Models\Comment::where("user_id", auth()->user()->id)->where("residence_id", $residence->id)->first(); @endphp
                @if(!$userComment)
                    <button class="write-review-btn" @click="showForm = !showForm" x-text="showForm ? '✖ بستن فرم' : '✍️ نوشتن نظر'"></button>
                @endif
            @endauth
        </div>

        @auth
            @if(!$userComment)
                <div class="review-form-card" x-show="showForm" x-transition.duration.300ms>
                    <div class="form-header">
                        <i class="fa fa-pencil-square-o"></i>
                        <h4>نظر خود را بنویسید</h4>
                    </div>
                    <div class="form-body">
                        <div class="rating-input-group">
                            <span class="rating-label">امتیاز شما:</span>
                            <div class="stars-input">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" id="star_new_{{ $i }}" value="{{ $i }}" wire:model="point">
                                    <label for="star_new_{{ $i }}" @mouseover="hoverStar({{ $i }})" @mouseout="hoverStar(0)">★</label>
                                @endfor
                            </div>
                            <div class="rating-feedback" x-show="selectedRating > 0" x-text="ratingMessages[selectedRating]" x-cloak></div>
                            @error('point') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="comment-input-group">
                            <textarea wire:model="commentText" rows="4" placeholder="تجربه خود را بنویسید..."></textarea>
                            <div class="char-counter" x-text="commentLength + '/500 کاراکتر'"></div>
                            @error('commentText') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        <button wire:click="submitComment" class="btn-submit-review" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa fa-send"></i> ثبت نظر</span>
                            <span wire:loading><i class="fa fa-spinner fa-spin"></i> در حال ثبت...</span>
                        </button>
                    </div>
                </div>
            @else
                <div class="review-alert already-reviewed">
                    <i class="fa fa-check-circle"></i> شما قبلاً نظر خود را ثبت کرده‌اید.
                </div>
            @endif
        @else
            <div class="review-alert login-required">
                <i class="fa fa-lock"></i> برای ثبت نظر <a href="{{ url('login') }}">وارد شوید</a>
            </div>
        @endauth

        @php $comments = \App\Models\Comment::where("residence_id", $residence->id)->orderBy("id", "desc")->get(); @endphp
        @if($comments->count() > 0)
            <div class="reviews-list">
                <div class="list-header"><i class="fa fa-users"></i><span>{{ $comments->count() }} دیدگاه</span></div>
                @foreach($comments as $index => $comment)
                    <div class="review-card" style="animation-delay: {{ $index * 0.05 }}s">
                        <div class="review-sidebar">
                            <div class="user-avatar">
                                @if($comment->user && $comment->user->profile_image)
                                    <img src="{{ asset('storage/user/' . $comment->user->profile_image) }}" alt="{{ $comment->user->name ?? 'کاربر' }}">
                                @else
                                    <i class="fa fa-user-circle"></i>
                                @endif
                            </div>
                            <div class="user-name">{{ $comment->user->name ?? 'کاربر' }}</div>
                            <div class="review-date">{{ $comment->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="review-content">
                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $comment->point) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                                @endfor
                            </div>
                            <p class="review-text">{{ $comment->comment }}</p>
                            <div class="review-footer">
                                <button class="like-btn" onclick="likeComment({{ $comment->id }})">
                                    <i class="fa fa-thumbs-o-up"></i> <span class="like-count">0</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="review-empty"><i class="fa fa-comment-o"></i><p>هنوز دیدگاهی ثبت نشده است.</p><span>اولین نفری باشید که نظر می‌نویسد!</span></div>
        @endif
    </div>

    {{-- اقامتگاه‌های مشابه --}}
    @php
        $similarResidences = \App\Models\Residence::where("city_id", $residence->city_id)->where("id", "!=", $residence->id)->limit(6)->get();
    @endphp
    
    @if($similarResidences->count() > 0)
        <div class="similar-section">
            <h3 class="similar-title">🏡 اقامتگاه‌های مشابه در {{ $residence->city->name }}</h3>
            <div class="similar-grid">
                @foreach($similarResidences as $similar)
                    @php $similarImg = asset('storage/residences/' . $similar->image); if (!file_exists(public_path('storage/residences/' . $similar->image))) { $similarImg = $defaultImage; } @endphp
                    <div class="similar-card">
                        <img src="{{ $similarImg }}" alt="{{ $similar->title }}" loading="lazy" onerror="this.src='{{ $defaultImage }}'">
                        <div class="similar-card-content">
                            <h4 class="similar-card-title">{{ Str::limit($similar->title, 35) }}</h4>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                                <span class="similar-price">{{ number_format($similar->amount) }}<small> تومان</small></span>
                                <a href="{{ url('detail/' . $similar->id) }}" class="similar-view-btn">مشاهده</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif


    <script>
        let checkinDate = null;
        let checkoutDate = null;
        const nightlyPrice = {{ $residence->amount }};
        
        $('#checkin_date').pDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            onSelect: function(unix) {
                checkinDate = unix;
                $('#checkout_date').val('');
                checkoutDate = null;
                $('#nightsInfo').hide();
                $('#priceDetail').hide();
            }
        });
        
        $('#checkout_date').pDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            onSelect: function(unix) {
                if (!checkinDate) {
                    alert('ابتدا تاریخ ورود را انتخاب کنید');
                    $('#checkout_date').val('');
                    return;
                }
                if (unix <= checkinDate) {
                    alert('تاریخ خروج باید بعد از تاریخ ورود باشد');
                    $('#checkout_date').val('');
                    return;
                }
                checkoutDate = unix;
                const nights = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));
                const total = nightlyPrice * nights;
                
                $('#nightsCount').text(nights);
                $('#priceNights').text(nights);
                $('#totalPrice').text(total.toLocaleString() + ' تومان');
                $('#finalTotal').text(total.toLocaleString() + ' تومان');
                $('#nightsInfo').show();
                $('#priceDetail').show();
            }
        });

        
        let currentImageIndex = 0;
        let lightboxImages = [];
        
        function changeGalleryImageQuick(src, element, index) {
            document.getElementById('mainGalleryImg').src = src;
            document.querySelectorAll('.thumb-item').forEach(item => item.classList.remove('thumb-active'));
            element.classList.add('thumb-active');
            currentImageIndex = index;
        }
        
        function openLightbox(images, index) {
            lightboxImages = images;
            currentImageIndex = index;
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightboxImg');
            const counter = document.getElementById('lightboxCounter');
            if (lightboxImages.length > 0) {
                lightboxImg.src = lightboxImages[currentImageIndex];
                counter.textContent = (currentImageIndex + 1) + ' / ' + lightboxImages.length;
                lightbox.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = '';
        }
        
        function changeLightboxImage(direction) {
            currentImageIndex += direction;
            if (currentImageIndex < 0) currentImageIndex = lightboxImages.length - 1;
            if (currentImageIndex >= lightboxImages.length) currentImageIndex = 0;
            document.getElementById('lightboxImg').src = lightboxImages[currentImageIndex];
            document.getElementById('lightboxCounter').textContent = (currentImageIndex + 1) + ' / ' + lightboxImages.length;
            document.getElementById('mainGalleryImg').src = lightboxImages[currentImageIndex];
            document.querySelectorAll('.thumb-item').forEach((thumb, idx) => {
                if (idx === currentImageIndex) thumb.classList.add('thumb-active');
                else thumb.classList.remove('thumb-active');
            });
        }
        
        document.addEventListener('keydown', function(e) {
            const lightbox = document.getElementById('lightbox');
            if (lightbox.classList.contains('active')) {
                if (e.key === 'ArrowLeft') changeLightboxImage(-1);
                else if (e.key === 'ArrowRight') changeLightboxImage(1);
                else if (e.key === 'Escape') closeLightbox();
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            var mapContainer = document.getElementById('map');
            if (mapContainer) {
                var lat = {{ $residence->lat ?? 0 }};
                var lng = {{ $residence->lng ?? 0 }};
                if (lat != 0 && lng != 0) {
                    mapContainer.innerHTML = `<div class="map-card"><div class="map-icon"><i class="fa fa-map-marker"></i></div><h4>موقعیت مکانی اقامتگاه</h4><p>استان {{ $residence->province->name }}، {{ $residence->city->name }}</p><p class="map-address">{{ $residence->address ?? 'آدرس دقیق در تماس اعلام می‌شود' }}</p><a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" class="map-btn"><i class="fa fa-external-link"></i> مسیریابی با گوگل مپ</a></div>`;
                } else {
                    mapContainer.innerHTML = `<div class="map-placeholder"><i class="fa fa-map-marker"></i><p>موقعیت مکانی این اقامتگاه ثبت نشده است</p></div>`;
                }
            }
        });
        
        document.getElementById('shareBtn')?.addEventListener('click', function() {
            if (navigator.share) navigator.share({ title: '{{ $residence->title }}', url: window.location.href });
            else { navigator.clipboard.writeText(window.location.href); alert('لینک کپی شد'); }
        });
        
        
        document.addEventListener('alpine:init', () => {
            Alpine.data('reviewsComponent', () => ({
                showForm: false, selectedRating: 0, commentLength: 0,
                ratingMessages: { 1: '😞 خیلی ضعیف', 2: '🙁 قابل قبول نیست', 3: '😐 معمولی', 4: '🙂 خوب', 5: '😍 عالی!' },
                hoverStar(star) { this.selectedRating = star; },
                updateCharCount() { this.commentLength = this.$wire.commentText?.length || 0; }
            }));
        });
        
        function likeComment(id) { console.log('لایک نظر:', id); }
    </script>
    
    @if(session('comment'))
        <script>Swal.fire({ icon: "success", title: "{{ session('comment') }}", timer: 3000, showConfirmButton: false });</script>
        @php session()->forget('comment'); @endphp
    @endif
</div>