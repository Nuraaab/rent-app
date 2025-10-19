<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Inquiry</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', 'Helvetica', sans-serif;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 10px;
        }
        .tagline {
            font-size: 14px;
            color: #ffffff;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #1a1a1a;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            font-size: 16px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .property-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            margin: 30px 0;
        }
        .property-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .property-details {
            padding: 20px;
            background-color: #F0F8FF;
        }
        .property-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 12px;
        }
        .property-location {
            font-size: 14px;
            color: #666666;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
        }
        .specs-row {
            display: flex;
            gap: 20px;
            margin-bottom: 16px;
        }
        .spec-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: #4A90E2;
            font-weight: 600;
        }
        .price {
            font-size: 24px;
            font-weight: 700;
            color: #4A90E2;
            margin-top: 12px;
        }
        .info-box {
            background-color: #F0F8FF;
            border-left: 4px solid #4A90E2;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .info-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .info-text {
            font-size: 14px;
            color: #666666;
            line-height: 1.6;
        }
        .inquirer-info {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .inquirer-label {
            font-size: 12px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .inquirer-value {
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            font-size: 14px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .app-name {
            font-size: 18px;
            font-weight: 700;
            color: #4A90E2;
            margin-bottom: 10px;
        }
        .social-links {
            margin-top: 20px;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Logo -->
        <div class="header">
            <div class="logo">🏠 SpaceGig</div>
            <div class="tagline">Find Your Perfect Place & Dream Career</div>
        </div>

        <!-- Main Content -->
        <div class="content">
            @if($isForOwner)
                <!-- Email for Property Owner -->
                <div class="greeting">Hi {{ $rental->user->first_name ?? 'Property Owner' }},</div>
                
                <div class="message">
                    Great news! Someone is interested in your property listing. We've received an inquiry about your property listed on SpaceGig.
                </div>

                <div class="inquirer-info">
                    <div class="inquirer-label">Inquirer Details</div>
                    <div class="inquirer-value">
                        <strong>Name:</strong> {{ $inquirer->first_name }} {{ $inquirer->last_name }}
                    </div>
                    @if($inquirer->email)
                        <div class="inquirer-value">
                            <strong>Email:</strong> {{ $inquirer->email }}
                        </div>
                    @endif
                    @if($inquirer->phone_number)
                        <div class="inquirer-value">
                            <strong>Phone:</strong> {{ $inquirer->phone_number }}
                        </div>
                    @endif
                    @if($moveInDate)
                        <div class="inquirer-value">
                            <strong>Preferred Move-in Date:</strong> {{ $moveInDate }}
                        </div>
                    @endif
                </div>

                @if($message)
                    <div class="info-box">
                        <div class="info-title">Message from Inquirer</div>
                        <div class="info-text">
                            "{{ $message }}"
                        </div>
                    </div>
                @endif

                <div class="message">
                    Here are the details of the property they're interested in:
                </div>
            @else
                <!-- Email for Inquirer -->
                <div class="greeting">Hi {{ $inquirer->first_name ?? 'there' }},</div>
                
                <div class="message">
                    Thank you for your interest! We've successfully received your inquiry about the property listed on SpaceGig.
                </div>

                <div class="info-box">
                    <div class="info-title">What happens next?</div>
                    <div class="info-text">
                        We've notified the property owner about your inquiry. They will review your request and contact you directly using the contact information you provided. You can expect to hear from them within 24-48 hours.
                    </div>
                </div>

                @if($message)
                    <div class="message" style="margin: 20px 0;">
                        <strong>Your Message:</strong><br>
                        <div style="background-color: #f9fafb; padding: 16px; border-radius: 8px; margin-top: 10px;">
                            "{{ $message }}"
                        </div>
                    </div>
                @endif

                @if($moveInDate)
                    <div class="message" style="margin: 20px 0;">
                        <strong>Preferred Move-in Date:</strong> {{ $moveInDate }}
                    </div>
                @endif

                <div class="message">
                    Here are the details of the property you inquired about:
                </div>
            @endif

            <!-- Property Card -->
            <div class="property-card">
                @if($rental->houseGallery && count($rental->houseGallery) > 0)
                    <img src="{{ url($rental->houseGallery[0]->gallery_path) }}" 
                         alt="{{ $rental->title }}" 
                         class="property-image">
                @else
                    <div style="width: 100%; height: 250px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 48px;">🏠</span>
                    </div>
                @endif
                
                <div class="property-details">
                    <div class="property-title">{{ $rental->title }}</div>
                    
                    <div class="property-location">
                        📍 {{ $rental->address ?? $rental->city ?? 'Location not specified' }}
                    </div>

                    <div class="specs-row">
                        @if($rental->number_of_bedrooms)
                            <div class="spec-item">
                                🛏️ {{ $rental->number_of_bedrooms }} Bedrooms
                            </div>
                        @endif
                        @if($rental->number_of_baths)
                            <div class="spec-item">
                                🛁 {{ $rental->number_of_baths }} Bathrooms
                            </div>
                        @endif
                        @if($rental->sqft)
                            <div class="spec-item">
                                📐 {{ $rental->sqft }} sq ft
                            </div>
                        @endif
                    </div>

                    @if($rental->price)
                        <div class="price">
                            ${{ number_format($rental->price) }}
                            @if($rental->listing_type && strtolower($rental->listing_type) !== 'sale')
                                /month
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if(!$isForOwner)
                <div class="divider"></div>

                <div class="info-box">
                    <div class="info-title">About SpaceGig</div>
                    <div class="info-text">
                        SpaceGig is your trusted platform for finding properties and job opportunities. 
                        We connect people with their perfect homes and dream careers. Our platform makes it 
                        easy for property seekers and landlords to communicate directly, ensuring a smooth 
                        and transparent experience for everyone.
                    </div>
                </div>
            @else
                <div class="divider"></div>

                <div class="message">
                    Please reach out to the inquirer at your earliest convenience. Prompt responses help build trust and increase the likelihood of successful transactions.
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="app-name">SpaceGig</div>
            <div class="footer-text">
                Find Your Perfect Place & Dream Career<br>
                Your trusted platform for properties and opportunities
            </div>
            <div class="footer-text" style="font-size: 12px; color: #999999; margin-top: 20px;">
                © {{ date('Y') }} SpaceGig. All rights reserved.<br>
                This email was sent because an inquiry was made through our platform.
            </div>
        </div>
    </div>
</body>
</html>

