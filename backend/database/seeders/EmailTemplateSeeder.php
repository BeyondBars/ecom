<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing templates
        EmailTemplate::truncate();
        
        // 🛒 Order / Checkout Templates
        EmailTemplate::create([
            'name' => 'Order Confirmation',
            'subject' => 'Your Order #{{order.id}} has been confirmed',
            'body' => $this->getOrderConfirmationTemplate(),
            'type' => 'transactional',
            'module' => 'order',
            'triggered_by' => 'order.created',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Order Shipped',
            'subject' => 'Your Order #{{order.id}} has been shipped',
            'body' => $this->getOrderShippedTemplate(),
            'type' => 'transactional',
            'module' => 'order',
            'triggered_by' => 'order.shipped',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Order Delivered',
            'subject' => 'Your Order #{{order.id}} has been delivered',
            'body' => $this->getOrderDeliveredTemplate(),
            'type' => 'transactional',
            'module' => 'order',
            'triggered_by' => 'order.delivered',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Order Cancelled',
            'subject' => 'Your Order #{{order.id}} has been cancelled',
            'body' => $this->getOrderCancelledTemplate(),
            'type' => 'transactional',
            'module' => 'order',
            'triggered_by' => 'order.cancelled',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Order Refunded',
            'subject' => 'Your Order #{{order.id}} has been refunded',
            'body' => $this->getOrderRefundedTemplate(),
            'type' => 'transactional',
            'module' => 'order',
            'triggered_by' => 'order.refunded',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Order Failed Payment',
            'subject' => 'Payment Failed for Order #{{order.id}}',
            'body' => $this->getOrderFailedPaymentTemplate(),
            'type' => 'transactional',
            'module' => 'payment',
            'triggered_by' => 'payment.failed',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Invoice',
            'subject' => 'Invoice #{{invoice.id}} for Your Order',
            'body' => $this->getInvoiceTemplate(),
            'type' => 'transactional',
            'module' => 'invoice',
            'triggered_by' => 'invoice.created',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Partial Shipment',
            'subject' => 'Partial Shipment for Order #{{order.id}}',
            'body' => $this->getPartialShipmentTemplate(),
            'type' => 'transactional',
            'module' => 'order',
            'triggered_by' => 'order.partial_shipped',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Backorder Notification',
            'subject' => 'Item(s) from Order #{{order.id}} on Backorder',
            'body' => $this->getBackorderTemplate(),
            'type' => 'transactional',
            'module' => 'order',
            'triggered_by' => 'order.backorder',
            'is_active' => true,
        ]);

        // 👤 User / Account Templates
        EmailTemplate::create([
            'name' => 'Welcome Email',
            'subject' => 'Welcome to {{site.name}}, {{user.name}}!',
            'body' => $this->getWelcomeTemplate(),
            'type' => 'transactional',
            'module' => 'user',
            'triggered_by' => 'user.registered',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Email Verification',
            'subject' => 'Verify Your Email Address',
            'body' => $this->getEmailVerificationTemplate(),
            'type' => 'transactional',
            'module' => 'user',
            'triggered_by' => 'user.email_verification',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Password Reset',
            'subject' => 'Reset Your Password',
            'body' => $this->getPasswordResetTemplate(),
            'type' => 'transactional',
            'module' => 'user',
            'triggered_by' => 'user.password_reset',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Password Changed',
            'subject' => 'Your Password Has Been Changed',
            'body' => $this->getPasswordChangedTemplate(),
            'type' => 'transactional',
            'module' => 'user',
            'triggered_by' => 'user.password_changed',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Account Deactivation',
            'subject' => 'Your Account Has Been Deactivated',
            'body' => $this->getAccountDeactivationTemplate(),
            'type' => 'transactional',
            'module' => 'user',
            'triggered_by' => 'user.deactivated',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Account Reactivation',
            'subject' => 'Your Account Has Been Reactivated',
            'body' => $this->getAccountReactivationTemplate(),
            'type' => 'transactional',
            'module' => 'user',
            'triggered_by' => 'user.reactivated',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Two-Factor Authentication Code',
            'subject' => 'Your Two-Factor Authentication Code',
            'body' => $this->getTwoFactorAuthTemplate(),
            'type' => 'transactional',
            'module' => 'user',
            'triggered_by' => 'user.2fa_code',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Email Address Changed',
            'subject' => 'Your Email Address Has Been Changed',
            'body' => $this->getEmailChangedTemplate(),
            'type' => 'transactional',
            'module' => 'user',
            'triggered_by' => 'user.email_changed',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Profile Update',
            'subject' => 'Your Profile Has Been Updated',
            'body' => $this->getProfileUpdateTemplate(),
            'type' => 'transactional',
            'module' => 'user',
            'triggered_by' => 'user.profile_updated',
            'is_active' => true,
        ]);

        // 💰 Payment / Refunds Templates
        EmailTemplate::create([
            'name' => 'Payment Successful',
            'subject' => 'Payment Successful for Order #{{order.id}}',
            'body' => $this->getPaymentSuccessfulTemplate(),
            'type' => 'transactional',
            'module' => 'payment',
            'triggered_by' => 'payment.successful',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Payment Failed',
            'subject' => 'Payment Failed for Order #{{order.id}}',
            'body' => $this->getPaymentFailedTemplate(),
            'type' => 'transactional',
            'module' => 'payment',
            'triggered_by' => 'payment.failed',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Refund Processed',
            'subject' => 'Refund Processed for Order #{{order.id}}',
            'body' => $this->getRefundProcessedTemplate(),
            'type' => 'transactional',
            'module' => 'payment',
            'triggered_by' => 'payment.refunded',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Subscription Renewal Reminder',
            'subject' => 'Your Subscription Will Renew Soon',
            'body' => $this->getSubscriptionRenewalTemplate(),
            'type' => 'transactional',
            'module' => 'subscription',
            'triggered_by' => 'subscription.renewal_reminder',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Subscription Cancelled',
            'subject' => 'Your Subscription Has Been Cancelled',
            'body' => $this->getSubscriptionCancelledTemplate(),
            'type' => 'transactional',
            'module' => 'subscription',
            'triggered_by' => 'subscription.cancelled',
            'is_active' => true,
        ]);

        // 🎁 Promotions / Marketing Templates
        EmailTemplate::create([
            'name' => 'Abandoned Cart Reminder',
            'subject' => 'You Left Items in Your Cart!',
            'body' => $this->getAbandonedCartTemplate(),
            'type' => 'marketing',
            'module' => 'cart',
            'triggered_by' => 'cart.abandoned',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Special Offer',
            'subject' => 'Special Offer Just for You!',
            'body' => $this->getSpecialOfferTemplate(),
            'type' => 'marketing',
            'module' => 'promotion',
            'triggered_by' => 'promotion.special_offer',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Seasonal Sale',
            'subject' => '{{promotion.name}} Sale is Now Live!',
            'body' => $this->getSeasonalSaleTemplate(),
            'type' => 'marketing',
            'module' => 'promotion',
            'triggered_by' => 'promotion.seasonal_sale',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Product Recommendation',
            'subject' => 'Products We Think You\'ll Love',
            'body' => $this->getProductRecommendationTemplate(),
            'type' => 'marketing',
            'module' => 'product',
            'triggered_by' => 'product.recommendation',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Loyalty Points Update',
            'subject' => 'Your Loyalty Points Update',
            'body' => $this->getLoyaltyPointsTemplate(),
            'type' => 'transactional',
            'module' => 'loyalty',
            'triggered_by' => 'loyalty.points_update',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Birthday Offer',
            'subject' => 'Happy Birthday, {{user.name}}! Here\'s a Gift',
            'body' => $this->getBirthdayOfferTemplate(),
            'type' => 'marketing',
            'module' => 'promotion',
            'triggered_by' => 'promotion.birthday_offer',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Newsletter Subscription',
            'subject' => 'Thank You for Subscribing to Our Newsletter',
            'body' => $this->getNewsletterSubscriptionTemplate(),
            'type' => 'transactional',
            'module' => 'newsletter',
            'triggered_by' => 'newsletter.subscribed',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Newsletter Content',
            'subject' => '{{newsletter.subject}}',
            'body' => $this->getNewsletterContentTemplate(),
            'type' => 'marketing',
            'module' => 'newsletter',
            'triggered_by' => 'newsletter.send',
            'is_active' => true,
        ]);

        // 📦 Shipping / Delivery Templates
        EmailTemplate::create([
            'name' => 'Shipping Confirmation',
            'subject' => 'Your Order #{{order.id}} Has Been Shipped',
            'body' => $this->getShippingConfirmationTemplate(),
            'type' => 'transactional',
            'module' => 'shipping',
            'triggered_by' => 'shipping.confirmed',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Tracking Number',
            'subject' => 'Tracking Information for Order #{{order.id}}',
            'body' => $this->getTrackingNumberTemplate(),
            'type' => 'transactional',
            'module' => 'shipping',
            'triggered_by' => 'shipping.tracking_available',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Delivery Attempt Failed',
            'subject' => 'Delivery Attempt Failed for Order #{{order.id}}',
            'body' => $this->getDeliveryAttemptFailedTemplate(),
            'type' => 'transactional',
            'module' => 'shipping',
            'triggered_by' => 'shipping.delivery_failed',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Pickup Available',
            'subject' => 'Your Order #{{order.id}} is Ready for Pickup',
            'body' => $this->getPickupAvailableTemplate(),
            'type' => 'transactional',
            'module' => 'shipping',
            'triggered_by' => 'shipping.pickup_available',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Expected Delivery Date Update',
            'subject' => 'Updated Delivery Date for Order #{{order.id}}',
            'body' => $this->getDeliveryDateUpdateTemplate(),
            'type' => 'transactional',
            'module' => 'shipping',
            'triggered_by' => 'shipping.delivery_date_update',
            'is_active' => true,
        ]);

        // 📋 Product Related Templates
        EmailTemplate::create([
            'name' => 'Back in Stock',
            'subject' => '{{product.name}} is Back in Stock!',
            'body' => $this->getBackInStockTemplate(),
            'type' => 'transactional',
            'module' => 'product',
            'triggered_by' => 'product.back_in_stock',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Price Drop Alert',
            'subject' => 'Price Drop Alert: {{product.name}}',
            'body' => $this->getPriceDropTemplate(),
            'type' => 'transactional',
            'module' => 'product',
            'triggered_by' => 'product.price_drop',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Product Review Request',
            'subject' => 'How Did You Like Your {{product.name}}?',
            'body' => $this->getProductReviewRequestTemplate(),
            'type' => 'marketing',
            'module' => 'review',
            'triggered_by' => 'review.request',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Review Published',
            'subject' => 'Your Review Has Been Published',
            'body' => $this->getReviewPublishedTemplate(),
            'type' => 'transactional',
            'module' => 'review',
            'triggered_by' => 'review.published',
            'is_active' => true,
        ]);

        // 📞 Support / Contact Templates
        EmailTemplate::create([
            'name' => 'Support Ticket Received',
            'subject' => 'Your Support Ticket #{{ticket.id}} Has Been Received',
            'body' => $this->getSupportTicketReceivedTemplate(),
            'type' => 'transactional',
            'module' => 'support',
            'triggered_by' => 'support.ticket_created',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Support Ticket Resolved',
            'subject' => 'Your Support Ticket #{{ticket.id}} Has Been Resolved',
            'body' => $this->getSupportTicketResolvedTemplate(),
            'type' => 'transactional',
            'module' => 'support',
            'triggered_by' => 'support.ticket_resolved',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Customer Feedback Acknowledgement',
            'subject' => 'Thank You for Your Feedback',
            'body' => $this->getCustomerFeedbackTemplate(),
            'type' => 'transactional',
            'module' => 'feedback',
            'triggered_by' => 'feedback.received',
            'is_active' => true,
        ]);

        // 🛠️ Admin / Internal Templates
        EmailTemplate::create([
            'name' => 'Low Stock Alert',
            'subject' => 'Low Stock Alert: {{product.name}}',
            'body' => $this->getLowStockAlertTemplate(),
            'type' => 'notification',
            'module' => 'admin',
            'triggered_by' => 'admin.low_stock',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'New Order Placed',
            'subject' => 'New Order #{{order.id}} Placed',
            'body' => $this->getNewOrderPlacedTemplate(),
            'type' => 'notification',
            'module' => 'admin',
            'triggered_by' => 'admin.new_order',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'New User Signup',
            'subject' => 'New User Signup: {{user.name}}',
            'body' => $this->getNewUserSignupTemplate(),
            'type' => 'notification',
            'module' => 'admin',
            'triggered_by' => 'admin.new_user',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'Failed Cron Job',
            'subject' => 'Failed Cron Job: {{cron.name}}',
            'body' => $this->getFailedCronJobTemplate(),
            'type' => 'notification',
            'module' => 'admin',
            'triggered_by' => 'admin.cron_failed',
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'name' => 'System Error',
            'subject' => 'System Error: {{error.type}}',
            'body' => $this->getSystemErrorTemplate(),
            'type' => 'notification',
            'module' => 'admin',
            'triggered_by' => 'admin.system_error',
            'is_active' => true,
        ]);
    }

    // 🛒 Order / Checkout Templates
    private function getOrderConfirmationTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Order Confirmation</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Thank you for your order! We\'re pleased to confirm that we\'ve received your order #{{order.id}}.</p>
                <p>Order Details:</p>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Order Number</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.id}}</td>
                    </tr>
                    <tr>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Order Date</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.date}}</td>
                    </tr>
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Payment Method</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.payment_method}}</td>
                    </tr>
                    <tr>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Shipping Method</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.shipping_method}}</td>
                    </tr>
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Order Total</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb; font-weight: bold;">{{order.total}}</td>
                    </tr>
                </table>
                
                <p>Order Items:</p>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Product</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">Quantity</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Price</th>
                    </tr>
                    {{#each order.items}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.name}}</td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">{{this.quantity}}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{this.price}}</td>
                    </tr>
                    {{/each}}
                </table>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <p style="margin: 0;"><strong>Shipping Address:</strong></p>
                    <p style="margin: 5px 0;">{{order.shipping_address.name}}</p>
                    <p style="margin: 5px 0;">{{order.shipping_address.street}}</p>
                    <p style="margin: 5px 0;">{{order.shipping_address.city}}, {{order.shipping_address.state}} {{order.shipping_address.zip}}</p>
                    <p style="margin: 5px 0;">{{order.shipping_address.country}}</p>
                </div>
                
                <p>We\'ll send you another email when your order ships.</p>
                <p>If you have any questions about your order, please contact our customer service team.</p>
                <p>Thank you for shopping with us!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getOrderShippedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Your Order Has Been Shipped!</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Great news! Your order #{{order.id}} has been shipped and is on its way to you.</p>
                <p>Tracking Information:</p>
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                    <p style="margin: 0; font-weight: bold;">Tracking Number: {{order.tracking_number}}</p>
                    <p style="margin: 10px 0 0 0;">
                        <a href="{{order.tracking_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Track Your Package</a>
                    </p>
                </div>
                
                <p>Estimated Delivery Date: {{order.estimated_delivery_date}}</p>
                
                <p>Order Details:</p>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Order Number</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.id}}</td>
                    </tr>
                    <tr>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Shipping Method</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.shipping_method}}</td>
                    </tr>
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Carrier</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.carrier}}</td>
                    </tr>
                </table>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <p style="margin: 0;"><strong>Shipping Address:</strong></p>
                    <p style="margin: 5px 0;">{{order.shipping_address.name}}</p>
                    <p style="margin: 5px 0;">{{order.shipping_address.street}}</p>
                    <p style="margin: 5px 0;">{{order.shipping_address.city}}, {{order.shipping_address.state}} {{order.shipping_address.zip}}</p>
                    <p style="margin: 5px 0;">{{order.shipping_address.country}}</p>
                </div>
                
                <p>If you have any questions about your shipment, please contact our customer service team.</p>
                <p>Thank you for shopping with us!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getOrderDeliveredTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Your Order Has Been Delivered!</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Great news! Your order #{{order.id}} has been delivered.</p>
                <p>We hope you enjoy your purchase. If you have any questions or concerns, please don\'t hesitate to contact our customer service team.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; font-weight: bold;">How was your experience?</p>
                    <p style="margin: 10px 0 0 0;">
                        <a href="{{site.url}}/review?order={{order.id}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Leave a Review</a>
                    </p>
                </div>
                
                <p>Order Details:</p>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Order Number</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.id}}</td>
                    </tr>
                    <tr>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Delivery Date</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.delivery_date}}</td>
                    </tr>
                </table>
                
                <p>Thank you for shopping with us!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getOrderCancelledTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Your Order Has Been Cancelled</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to confirm that your order #{{order.id}} has been cancelled as requested.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Cancellation Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Cancellation Date: {{order.cancellation_date}}</p>
                    <p style="margin: 5px 0;">Reason: {{order.cancellation_reason}}</p>
                </div>
                
                <p>Refund Information:</p>
                <p>If you\'ve already been charged for this order, a refund will be processed to your original payment method. Please allow 5-10 business days for the refund to appear in your account.</p>
                
                <p>If you have any questions about this cancellation or need further assistance, please contact our customer service team.</p>
                <p>We hope to serve you again in the future.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getOrderRefundedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Your Refund Has Been Processed</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to confirm that a refund for order #{{order.id}} has been processed.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Refund Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Refund Date: {{order.refund_date}}</p>
                    <p style="margin: 5px 0;">Refund Amount: {{order.refund_amount}}</p>
                    <p style="margin: 5px 0;">Refund Method: {{order.refund_method}}</p>
                </div>
                
                <p>Please note that it may take 5-10 business days for the refund to appear in your account, depending on your payment provider.</p>
                
                <p>If you have any questions about this refund or need further assistance, please contact our customer service team.</p>
                <p>Thank you for your understanding.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getOrderFailedPaymentTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Payment Failed</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re sorry to inform you that the payment for your order #{{order.id}} has failed.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Order Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Order Date: {{order.date}}</p>
                    <p style="margin: 5px 0;">Order Total: {{order.total}}</p>
                </div>
                
                <p>This could be due to one of the following reasons:</p>
                <ul>
                    <li>Insufficient funds in your account</li>
                    <li>Expired credit/debit card</li>
                    <li>Incorrect payment details</li>
                    <li>The transaction was flagged by your bank\'s security system</li>
                </ul>
                
                <p>To complete your purchase, please update your payment information:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/orders/{{order.id}}/payment" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Update Payment</a>
                </p>
                
                <p>If you need assistance or have any questions, please contact our customer service team.</p>
                <p>Thank you for your understanding.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getInvoiceTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Invoice for Your Order</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Please find attached the invoice for your order #{{order.id}}.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Invoice Details:</strong></p>
                    <p style="margin: 5px 0;">Invoice Number: {{invoice.id}}</p>
                    <p style="margin: 5px 0;">Invoice Date: {{invoice.date}}</p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Total Amount: {{invoice.total}}</p>
                </div>
                
                <p>You can also view and download your invoice from your account:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/account/invoices/{{invoice.id}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Invoice</a>
                </p>
                
                <p>If you have any questions about this invoice, please contact our customer service team.</p>
                <p>Thank you for your business!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getPartialShipmentTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Partial Shipment Notification</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to inform you that a portion of your order #{{order.id}} has been shipped and is on its way to you.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Shipment Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Tracking Number: {{shipment.tracking_number}}</p>
                    <p style="margin: 5px 0;">Carrier: {{shipment.carrier}}</p>
                    <p style="margin: 5px 0;">Estimated Delivery: {{shipment.estimated_delivery}}</p>
                </div>
                
                <p>Items in this shipment:</p>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Product</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">Quantity</th>
                    </tr>
                    {{#each shipment.items}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.name}}</td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">{{this.quantity}}</td>
                    </tr>
                    {{/each}}
                </table>
                
                <p>Track your shipment:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{shipment.tracking_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Track Package</a>
                </p>
                
                <p>The remaining items in your order will be shipped as soon as they become available. We\'ll send you another email when they\'re on their way.</p>
                
                <p>If you have any questions about your shipment, please contact our customer service team.</p>
                <p>Thank you for your patience and understanding.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getBackorderTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Backorder Notification</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to inform you that some items from your order #{{order.id}} are currently on backorder.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Order Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Order Date: {{order.date}}</p>
                </div>
                
                <p>The following items are on backorder:</p>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Product</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">Quantity</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Expected Availability</th>
                    </tr>
                    {{#each backorder.items}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.name}}</td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">{{this.quantity}}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{this.expected_date}}</td>
                    </tr>
                    {{/each}}
                </table>
                
                <p>We\'re working hard to get these items back in stock as soon as possible. Once they\'re available, we\'ll ship them to you immediately.</p>
                
                <p>Your options:</p>
                <ul>
                    <li>Wait for the backordered items (no action required)</li>
                    <li>Modify your order to remove backordered items</li>
                    <li>Cancel your order for a full refund</li>
                </ul>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/orders/{{order.id}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Manage Order</a>
                </p>
                
                <p>If you have any questions or concerns, please contact our customer service team.</p>
                <p>Thank you for your patience and understanding.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    // 👤 User / Account Templates
    private function getWelcomeTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Welcome to {{site.name}}!</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Thank you for creating an account with us. We\'re excited to have you join our community!</p>
                
                <p>With your new account, you can:</p>
                <ul>
                    <li>Shop our latest products</li>
                    <li>Track your orders</li>
                    <li>Save items to your wishlist</li>
                    <li>Receive exclusive offers</li>
                    <li>Manage your preferences</li>
                </ul>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/account" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Visit Your Account</a>
                </p>
                
                <p>If you have any questions or need assistance, please don\'t hesitate to contact our customer support team.</p>
                <p>Happy shopping!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getEmailVerificationTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Verify Your Email Address</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Thank you for creating an account with {{site.name}}. To complete your registration, please verify your email address by clicking the button below:</p>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{verification_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Verify Email Address</a>
                </p>
                
                <p>If the button above doesn\'t work, you can also copy and paste the following link into your browser:</p>
                <p style="background-color: #f3f4f6; padding: 10px; border-radius: 5px; word-break: break-all;">{{verification_url}}</p>
                
                <p>This link will expire in 24 hours.</p>
                
                <p>If you did not create an account with us, please disregard this email.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getPasswordResetTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Reset Your Password</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We received a request to reset your password. If you didn\'t make this request, you can safely ignore this email.</p>
                
                <p>To reset your password, click the button below:</p>
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{reset_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a>
                </p>
                
                <p>If the button above doesn\'t work, you can also copy and paste the following link into your browser:</p>
                <p style="background-color: #f3f4f6; padding: 10px; border-radius: 5px; word-break: break-all;">{{reset_url}}</p>
                
                <p>This link will expire in 60 minutes.</p>
                
                <p>If you did not request a password reset, please contact our customer support team immediately.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getPasswordChangedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Password Changed Successfully</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to confirm that your password for {{site.name}} has been successfully changed.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Account Information:</strong></p>
                    <p style="margin: 5px 0;">Email: {{user.email}}</p>
                    <p style="margin: 5px 0;">Password Changed: {{password_changed_date}}</p>
                </div>
                
                <p>If you did not make this change, please contact our customer support team immediately.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/contact" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Contact Support</a>
                </p>
                
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getAccountDeactivationTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Account Deactivated</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re sorry to see you go. As requested, your account has been deactivated.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Account Information:</strong></p>
                    <p style="margin: 5px 0;">Email: {{user.email}}</p>
                    <p style="margin: 5px 0;">Deactivation Date: {{deactivation_date}}</p>
                </div>
                
                <p>If you wish to reactivate your account in the future, you can do so by logging in with your email and password within the next 30 days. After that period, your account data may be permanently removed.</p>
                
                <p>We value your feedback. If you'd like to share the reason for deactivating your account, please let us know:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/feedback" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Share Feedback</a>
                </p>
                
                <p>Thank you for being a customer. We hope to see you again in the future.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getAccountReactivationTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Account Reactivated</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Great news! Your account has been successfully reactivated.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Account Information:</strong></p>
                    <p style="margin: 5px 0;">Email: {{user.email}}</p>
                    <p style="margin: 5px 0;">Reactivation Date: {{reactivation_date}}</p>
                </div>
                
                <p>Your account has been restored with all your previous information, including your order history, saved addresses, and preferences.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/account" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Visit Your Account</a>
                </p>
                
                <p>We're happy to have you back! If you have any questions or need assistance, please don't hesitate to contact our customer support team.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getTwoFactorAuthTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Your Authentication Code</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>You\'ve requested to log in to your {{site.name}} account. For your security, we require two-factor authentication.</p>
                
                <div style="background-color: #f3f4f6; padding: 20px; border-radius: 5px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 5px;">{{auth_code}}</p>
                </div>
                
                <p>Please enter this code on the verification page to complete your login. This code will expire in 10 minutes.</p>
                
                <p>If you did not attempt to log in to your account, please change your password immediately and contact our customer support team.</p>
                
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getEmailChangedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Email Address Changed</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to confirm that the email address associated with your {{site.name}} account has been changed.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Account Information:</strong></p>
                    <p style="margin: 5px 0;">Previous Email: {{user.old_email}}</p>
                    <p style="margin: 5px 0;">New Email: {{user.email}}</p>
                    <p style="margin: 5px 0;">Change Date: {{email_changed_date}}</p>
                </div>
                
                <p>If you did not make this change, please contact our customer support team immediately.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/contact" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Contact Support</a>
                </p>
                
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getProfileUpdateTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Profile Updated</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to confirm that your profile information has been successfully updated.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Account Information:</strong></p>
                    <p style="margin: 5px 0;">Email: {{user.email}}</p>
                    <p style="margin: 5px 0;">Update Date: {{profile_updated_date}}</p>
                </div>
                
                <p>If you did not make these changes, please contact our customer support team immediately.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/account" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Your Profile</a>
                </p>
                
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    // 💰 Payment / Refunds Templates
    private function getPaymentSuccessfulTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Payment Successful</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to confirm that your payment for order #{{order.id}} has been successfully processed.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Payment Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Payment Date: {{payment.date}}</p>
                    <p style="margin: 5px 0;">Payment Method: {{payment.method}}</p>
                    <p style="margin: 5px 0;">Amount: {{payment.amount}}</p>
                    <p style="margin: 5px 0;">Transaction ID: {{payment.transaction_id}}</p>
                </div>
                
                <p>Your order is now being processed. We\'ll send you another email when your order ships.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/orders/{{order.id}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Order</a>
                </p>
                
                <p>Thank you for your purchase!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getPaymentFailedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Payment Failed</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re sorry to inform you that your payment for order #{{order.id}} could not be processed.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Order Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Order Date: {{order.date}}</p>
                    <p style="margin: 5px 0;">Order Total: {{order.total}}</p>
                </div>
                
                <p>This could be due to one of the following reasons:</p>
                <ul>
                    <li>Insufficient funds in your account</li>
                    <li>Expired credit/debit card</li>
                    <li>Incorrect payment details</li>
                    <li>The transaction was flagged by your bank\'s security system</li>
                </ul>
                
                <p>To complete your purchase, please update your payment information:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/orders/{{order.id}}/payment" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Update Payment</a>
                </p>
                
                <p>If you need assistance or have any questions, please contact our customer service team.</p>
                <p>Thank you for your understanding.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getRefundProcessedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Refund Processed</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to confirm that a refund for order #{{order.id}} has been processed.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Refund Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Refund Date: {{refund.date}}</p>
                    <p style="margin: 5px 0;">Refund Amount: {{refund.amount}}</p>
                    <p style="margin: 5px 0;">Refund Method: {{refund.method}}</p>
                    <p style="margin: 5px 0;">Transaction ID: {{refund.transaction_id}}</p>
                </div>
                
                <p>Please note that it may take 5-10 business days for the refund to appear in your account, depending on your payment provider.</p>
                
                <p>If you have any questions about this refund or need further assistance, please contact our customer service team.</p>
                <p>Thank you for your understanding.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getSubscriptionRenewalTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Subscription Renewal Reminder</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>This is a friendly reminder that your subscription will renew automatically on {{subscription.renewal_date}}.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Subscription Details:</strong></p>
                    <p style="margin: 5px 0;">Subscription Plan: {{subscription.plan}}</p>
                    <p style="margin: 5px 0;">Renewal Date: {{subscription.renewal_date}}</p>
                    <p style="margin: 5px 0;">Renewal Amount: {{subscription.renewal_amount}}</p>
                    <p style="margin: 5px 0;">Payment Method: {{subscription.payment_method}}</p>
                </div>
                
                <p>If you wish to make any changes to your subscription, please do so before the renewal date.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/account/subscriptions" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Manage Subscription</a>
                </p>
                
                <p>Thank you for being a valued subscriber!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getSubscriptionCancelledTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Subscription Cancelled</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to confirm that your subscription has been cancelled as requested.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Subscription Details:</strong></p>
                    <p style="margin: 5px 0;">Subscription Plan: {{subscription.plan}}</p>
                    <p style="margin: 5px 0;">Cancellation Date: {{subscription.cancellation_date}}</p>
                    <p style="margin: 5px 0;">End of Service Date: {{subscription.end_date}}</p>
                </div>
                
                <p>You will continue to have access to your subscription benefits until the end of service date mentioned above.</p>
                
                <p>We\'d love to know why you decided to cancel. Your feedback helps us improve our service:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/feedback" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Share Feedback</a>
                </p>
                
                <p>If you change your mind, you can reactivate your subscription at any time before the end of service date:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/account/subscriptions" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Reactivate Subscription</a>
                </p>
                
                <p>Thank you for being a subscriber. We hope to serve you again in the future.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    // 🎁 Promotions / Marketing Templates
    private function getAbandonedCartTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">You Left Items in Your Cart</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We noticed that you left some items in your shopping cart. Don\'t worry, we\'ve saved them for you!</p>
                
                <p>Here\'s what\'s waiting in your cart:</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Product</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">Quantity</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Price</th>
                    </tr>
                    {{#each cart.items}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.name}}</td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">{{this.quantity}}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{this.price}}</td>
                    </tr>
                    {{/each}}
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;" colspan="2">Total</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb; font-weight: bold;">{{cart.total}}</td>
                    </tr>
                </table>
                
                <p>Ready to complete your purchase? Your cart is just a click away:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{cart.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Return to Cart</a>
                </p>
                
                <p>If you have any questions or need assistance with your purchase, our customer service team is here to help.</p>
                <p>Happy shopping!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getSpecialOfferTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Special Offer Just for You!</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re excited to offer you an exclusive deal on products we think you\'ll love!</p>
                
                <div style="background-color: #f3f4f6; padding: 20px; border-radius: 5px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; font-size: 18px;">Use code: <strong>{{promotion.code}}</strong></p>
                    <p style="margin: 10px 0 0 0; font-size: 24px; font-weight: bold;">{{promotion.discount}}</p>
                    <p style="margin: 10px 0 0 0;">Valid until: {{promotion.expiry_date}}</p>
                </div>
                
                <p>Here are some products you might be interested in:</p>
                
                <div style="display: flex; justify-content: space-between; margin: 20px 0;">
                    {{#each promotion.products}}
                    <div style="width: 30%; text-align: center;">
                        <img src="{{this.image}}" alt="{{this.name}}" style="width: 100%; height: auto; border-radius: 5px;">
                        <p style="margin: 10px 0 5px 0; font-weight: bold;">{{this.name}}</p>
                        <p style="margin: 0 0 10px 0;">{{this.price}}</p>
                        <a href="{{this.url}}" style="background-color: #4f46e5; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px;">Shop Now</a>
                    </div>
                    {{/each}}
                </div>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{promotion.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Shop All Deals</a>
                </p>
                
                <p>Don\'t miss out on these amazing offers!</p>
                <p>Happy shopping!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getSeasonalSaleTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">{{promotion.name}} Sale is Now Live!</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Our {{promotion.name}} Sale is here! Enjoy incredible savings across our entire store.</p>
                
                <div style="background-color: #f3f4f6; padding: 20px; border-radius: 5px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; font-size: 18px;">Save up to</p>
                    <p style="margin: 10px 0; font-size: 36px; font-weight: bold; color: #4f46e5;">{{promotion.discount}}</p>
                    <p style="margin: 10px 0 0 0;">Sale ends: {{promotion.end_date}}</p>
                </div>
                
                <p>Top Categories on Sale:</p>
                <ul>
                    {{#each promotion.categories}}
                    <li><a href="{{this.url}}" style="color: #4f46e5; text-decoration: none;">{{this.name}} - Up to {{this.discount}} off</a></li>
                    {{/each}}
                </ul>
                
                <p>Featured Products:</p>
                <div style="display: flex; justify-content: space-between; margin: 20px 0;">
                    {{#each promotion.featured_products}}
                    <div style="width: 30%; text-align: center;">
                        <img src="{{this.image}}" alt="{{this.name}}" style="width: 100%; height: auto; border-radius: 5px;">
                        <p style="margin: 10px 0 5px 0; font-weight: bold;">{{this.name}}</p>
                        <p style="margin: 0 0 5px 0;"><s>{{this.original_price}}</s> <span style="color: #ef4444; font-weight: bold;">{{this.sale_price}}</span></p>
                        <a href="{{this.url}}" style="background-color: #4f46e5; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px;">Shop Now</a>
                    </div>
                    {{/each}}
                </div>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{promotion.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Shop the Sale</a>
                </p>
                
                <p>Don\'t wait! These deals won\'t last long.</p>
                <p>Happy shopping!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getProductRecommendationTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Products We Think You\'ll Love</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Based on your recent purchases and browsing history, we\'ve handpicked some products we think you might enjoy.</p>
                
                <div style="margin: 30px 0;">
                    {{#each recommendations}}
                    <div style="display: flex; margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 20px;">
                        <div style="width: 30%;">
                            <img src="{{this.image}}" alt="{{this.name}}" style="width: 100%; height: auto; border-radius: 5px;">
                        </div>
                        <div style="width: 70%; padding-left: 15px;">
                            <h3 style="margin: 0 0 10px 0;">{{this.name}}</h3>
                            <p style="margin: 0 0 10px 0;">{{this.price}}</p>
                            <p style="margin: 0 0 10px 0;">{{this.description}}</p>
                            <a href="{{this.url}}" style="background-color: #4f46e5; color: white; padding: 5px 15px; text-decoration: none; border-radius: 5px; display: inline-block;">View Product</a>
                        </div>
                    </div>
                    {{/each}}
                </div>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{site.url}}/recommendations" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View All Recommendations</a>
                </p>
                
                <p>We hope you find something you love!</p>
                <p>Happy shopping!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getLoyaltyPointsTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Your Loyalty Points Update</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Here\'s an update on your loyalty points balance:</p>
                
                <div style="background-color: #f3f4f6; padding: 20px; border-radius: 5px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; font-size: 18px;">Current Points Balance</p>
                    <p style="margin: 10px 0; font-size: 36px; font-weight: bold; color: #4f46e5;">{{loyalty.current_points}}</p>
                    <p style="margin: 10px 0 0 0;">Points Value: {{loyalty.points_value}}</p>
                </div>
                
                <p>Recent Activity:</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Date</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Activity</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Points</th>
                    </tr>
                    {{#each loyalty.recent_activity}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.date}}</td>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.description}}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb; color: {{this.color}};">{{this.points}}</td>
                    </tr>
                    {{/each}}
                </table>
                
                <p>Points Expiring Soon:</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Expiry Date</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Points</th>
                    </tr>
                    {{#each loyalty.expiring_points}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.date}}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{this.points}}</td>
                    </tr>
                    {{/each}}
                </table>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{site.url}}/account/loyalty" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Your Loyalty Account</a>
                </p>
                
                <p>Thank you for your continued loyalty!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getBirthdayOfferTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Happy Birthday, {{user.name}}!</h1>
            </div>
            <div style="padding: 20px; text-align: center;">
                <p style="font-size: 18px;">We\'re celebrating your special day with a gift just for you!</p>
                
                <div style="background-color: #f3f4f6; padding: 20px; border-radius: 5px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; font-size: 18px;">Enjoy</p>
                    <p style="margin: 10px 0; font-size: 36px; font-weight: bold; color: #4f46e5;">{{birthday.discount}}</p>
                    <p style="margin: 10px 0 0 0;">Use code: <strong>{{birthday.code}}</strong></p>
                    <p style="margin: 10px 0 0 0;">Valid until: {{birthday.expiry_date}}</p>
                </div>
                
                <p>This special birthday offer can be used on any purchase from our store.</p>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{site.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Shop Now</a>
                </p>
                
                <p>We hope you have a wonderful birthday!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getNewsletterSubscriptionTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Thank You for Subscribing!</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Thank you for subscribing to our newsletter! We\'re excited to keep you updated with our latest products, promotions, and news.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Subscription Details:</strong></p>
                    <p style="margin: 5px 0;">Email: {{user.email}}</p>
                    <p style="margin: 5px 0;">Subscription Date: {{subscription_date}}</p>
                    <p style="margin: 5px 0;">Frequency: {{newsletter.frequency}}</p>
                </div>
                
                <p>As a welcome gift, here\'s a special offer just for you:</p>
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; font-size: 18px;">{{newsletter.welcome_offer}}</p>
                    <p style="margin: 10px 0 0 0;">Use code: <strong>{{newsletter.welcome_code}}</strong></p>
                    <p style="margin: 10px 0 0 0;">Valid until: {{newsletter.welcome_expiry}}</p>
                </div>
                
                <p>You can manage your subscription preferences at any time:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/account/preferences" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Manage Preferences</a>
                </p>
                
                <p>We\'re looking forward to sharing great content with you!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getNewsletterContentTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">{{newsletter.title}}</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                
                {{newsletter.content}}
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{newsletter.cta_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">{{newsletter.cta_text}}</a>
                </p>
                
                <p>We hope you enjoyed this newsletter!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{site.url}}/account/preferences" style="color: #6b7280; text-decoration: none;">Manage Preferences</a> | 
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe</a>
                </p>
            </div>
        </div>
        ';
    }

    // 📦 Shipping / Delivery Templates
    private function getShippingConfirmationTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Your Order Has Been Shipped</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Great news! Your order #{{order.id}} has been shipped and is on its way to you.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Shipping Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Shipping Date: {{shipping.date}}</p>
                    <p style="margin: 5px 0;">Carrier: {{shipping.carrier}}</p>
                    <p style="margin: 5px 0;">Tracking Number: {{shipping.tracking_number}}</p>
                    <p style="margin: 5px 0;">Estimated Delivery: {{shipping.estimated_delivery}}</p>
                </div>
                
                <p>Track your shipment:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{shipping.tracking_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Track Package</a>
                </p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Shipping Address:</strong></p>
                    <p style="margin: 5px 0;">{{shipping.address.name}}</p>
                    <p style="margin: 5px 0;">{{shipping.address.street}}</p>
                    <p style="margin: 5px 0;">{{shipping.address.city}}, {{shipping.address.state}} {{shipping.address.zip}}</p>
                    <p style="margin: 5px 0;">{{shipping.address.country}}</p>
                </div>
                
                <p>Items in this shipment:</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Product</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">Quantity</th>
                    </tr>
                    {{#each shipping.items}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.name}}</td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">{{this.quantity}}</td>
                    </tr>
                    {{/each}}
                </table>
                
                <p>If you have any questions about your shipment, please contact our customer service team.</p>
                <p>Thank you for shopping with us!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getTrackingNumberTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Tracking Information for Your Order</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Your order #{{order.id}} is on its way! Here\'s your tracking information:</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; font-weight: bold;">Tracking Number: {{shipping.tracking_number}}</p>
                    <p style="margin: 10px 0 0 0;">Carrier: {{shipping.carrier}}</p>
                    <p style="margin: 10px 0 0 0;">Estimated Delivery: {{shipping.estimated_delivery}}</p>
                    <p style="margin: 20px 0 0 0;">
                        <a href="{{shipping.tracking_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Track Your Package</a>
                    </p>
                </div>
                
                <p>You can also view your order status and tracking information in your account:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/orders/{{order.id}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Order</a>
                </p>
                
                <p>If you have any questions about your shipment, please contact our customer service team.</p>
                <p>Thank you for shopping with us!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getDeliveryAttemptFailedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Delivery Attempt Failed</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to inform you that a delivery attempt was made for your order #{{order.id}}, but the carrier was unable to deliver your package.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Delivery Information:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Delivery Attempt: {{delivery.attempt_date}}</p>
                    <p style="margin: 5px 0;">Carrier: {{delivery.carrier}}</p>
                    <p style="margin: 5px 0;">Tracking Number: {{delivery.tracking_number}}</p>
                    <p style="margin: 5px 0;">Reason: {{delivery.failure_reason}}</p>
                </div>
                
                <p>Next Steps:</p>
                <ul>
                    {{#if delivery.next_attempt}}
                    <li>The carrier will attempt delivery again on {{delivery.next_attempt}}</li>
                    {{/if}}
                    {{#if delivery.pickup_available}}
                    <li>Your package is available for pickup at the carrier\'s location</li>
                    {{/if}}
                    {{#if delivery.action_required}}
                    <li>Action required: {{delivery.action_required}}</li>
                    {{/if}}
                </ul>
                
                <p>Track your shipment or manage delivery options:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{delivery.tracking_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration  style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Track Package</a>
                </p>
                
                <p>If you have any questions or need assistance, please contact our customer service team.</p>
                <p>Thank you for your understanding.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getPickupAvailableTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Your Order is Ready for Pickup</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Great news! Your order #{{order.id}} is now ready for pickup.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Pickup Information:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Pickup Location: {{pickup.location_name}}</p>
                    <p style="margin: 5px 0;">Address: {{pickup.address}}</p>
                    <p style="margin: 5px 0;">Hours: {{pickup.hours}}</p>
                    <p style="margin: 5px 0;">Available Until: {{pickup.available_until}}</p>
                </div>
                
                <p>What to bring:</p>
                <ul>
                    <li>Your order confirmation (printed or on your phone)</li>
                    <li>A valid photo ID</li>
                    <li>The payment card used for the purchase (if applicable)</li>
                </ul>
                
                <p>If someone else will be picking up your order, please contact our customer service team in advance.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{site.url}}/orders/{{order.id}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Order Details</a>
                </p>
                
                <p>If you have any questions, please contact our customer service team.</p>
                <p>Thank you for shopping with us!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getDeliveryDateUpdateTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Delivery Date Update</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re writing to inform you that there has been an update to the estimated delivery date for your order #{{order.id}}.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Delivery Update:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Previous Estimated Delivery: {{delivery.previous_date}}</p>
                    <p style="margin: 5px 0;">New Estimated Delivery: {{delivery.new_date}}</p>
                    <p style="margin: 5px 0;">Reason: {{delivery.reason}}</p>
                </div>
                
                <p>Track your shipment:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{delivery.tracking_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Track Package</a>
                </p>
                
                <p>We apologize for any inconvenience this may cause. If you have any questions or concerns, please contact our customer service team.</p>
                <p>Thank you for your patience and understanding.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    // 📋 Product Related Templates
    private function getBackInStockTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">{{product.name}} is Back in Stock!</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Good news! The item you\'ve been waiting for is now back in stock.</p>
                
                <div style="display: flex; margin: 20px 0; border: 1px solid #e5e7eb; border-radius: 5px; overflow: hidden;">
                    <div style="width: 30%; padding: 10px;">
                        <img src="{{product.image}}" alt="{{product.name}}" style="width: 100%; height: auto; border-radius: 5px;">
                    </div>
                    <div style="width: 70%; padding: 15px;">
                        <h3 style="margin: 0 0 10px 0;">{{product.name}}</h3>
                        <p style="margin: 0 0 10px 0;">{{product.price}}</p>
                        <p style="margin: 0 0 10px 0;">{{product.description}}</p>
                        <a href="{{product.url}}" style="background-color: #4f46e5; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block;">View Product</a>
                    </div>
                </div>
                
                <p>Don\'t wait too long! Popular items can sell out quickly.</p>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{product.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Shop Now</a>
                </p>
                
                <p>Thank you for your patience!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe from stock notifications</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getPriceDropTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Price Drop Alert!</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Good news! An item on your wishlist has dropped in price.</p>
                
                <div style="display: flex; margin: 20px 0; border: 1px solid #e5e7eb; border-radius: 5px; overflow: hidden;">
                    <div style="width: 30%; padding: 10px;">
                        <img src="{{product.image}}" alt="{{product.name}}" style="width: 100%; height: auto; border-radius: 5px;">
                    </div>
                    <div style="width: 70%; padding: 15px;">
                        <h3 style="margin: 0 0 10px 0;">{{product.name}}</h3>
                        <p style="margin: 0 0 10px 0;"><s>{{product.original_price}}</s> <span style="color: #ef4444; font-weight: bold;">{{product.new_price}}</span></p>
                        <p style="margin: 0 0 10px 0;">You Save: {{product.savings}} ({{product.savings_percent}})</p>
                        <a href="{{product.url}}" style="background-color: #4f46e5; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block;">View Product</a>
                    </div>
                </div>
                
                <p>This price won\'t last forever! Take advantage of this deal while it lasts.</p>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{product.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Shop Now</a>
                </p>
                
                <p>Happy shopping!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe from price alerts</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getProductReviewRequestTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">How Did You Like Your Purchase?</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Thank you for your recent purchase! We hope you\'re enjoying your new {{product.name}}.</p>
                
                <div style="display: flex; margin: 20px 0; border: 1px solid #e5e7eb; border-radius: 5px; overflow: hidden;">
                    <div style="width: 30%; padding: 10px;">
                        <img src="{{product.image}}" alt="{{product.name}}" style="width: 100%; height: auto; border-radius: 5px;">
                    </div>
                    <div style="width: 70%; padding: 15px;">
                        <h3 style="margin: 0 0 10px 0;">{{product.name}}</h3>
                        <p style="margin: 0 0 10px 0;">Order #: {{order.id}}</p>
                        <p style="margin: 0 0 10px 0;">Purchase Date: {{order.date}}</p>
                    </div>
                </div>
                
                <p>We\'d love to hear your thoughts! Your feedback helps other customers make informed decisions and helps us improve our products.</p>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{review.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Write a Review</a>
                </p>
                
                <p>It only takes a minute, and your insights are invaluable to our community.</p>
                <p>Thank you for being a valued customer!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280; text-decoration: none;">Unsubscribe from review requests</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getReviewPublishedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Your Review Has Been Published</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Thank you for taking the time to share your thoughts! Your review for {{product.name}} has been published on our website.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Your Review:</strong></p>
                    <div style="margin: 10px 0 0 0;">
                        <div style="margin-bottom: 5px;">
                            {{#for i in 1..review.rating}}
                            ★
                            {{/for}}
                            {{#for i in (review.rating+1)..5}}
                            ☆
                            {{/for}}
                        </div>
                        <p style="margin: 5px 0;"><strong>{{review.title}}</strong></p>
                        <p style="margin: 5px 0;">{{review.content}}</p>
                        <p style="margin: 5px 0; font-style: italic; color: #6b7280;">Posted on: {{review.date}}</p>
                    </div>
                </div>
                
                <p>You can view your review on the product page:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{product.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Your Review</a>
                </p>
                
                <p>Your feedback helps other customers make informed decisions. Thank you for being a part of our community!</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    // 📞 Support / Contact Templates
    private function getSupportTicketReceivedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Support Ticket Received</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Thank you for contacting our support team. We\'ve received your ticket and will get back to you as soon as possible.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Ticket Details:</strong></p>
                    <p style="margin: 5px 0;">Ticket Number: {{ticket.id}}</p>
                    <p style="margin: 5px 0;">Subject: {{ticket.subject}}</p>
                    <p style="margin: 5px 0;">Date Submitted: {{ticket.date}}</p>
                    <p style="margin: 5px 0;">Status: {{ticket.status}}</p>
                </div>
                
                <p>Your ticket summary:</p>
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0; font-style: italic;">{{ticket.message}}</p>
                </div>
                
                <p>You can view the status of your ticket and add additional information at any time:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{ticket.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Ticket</a>
                </p>
                
                <p>Our support team typically responds within 24 hours during business days.</p>
                <p>Thank you for your patience.</p>
                <p>Sincerely,<br>The {{site.name}} Support Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getSupportTicketResolvedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Support Ticket Resolved</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>We\'re pleased to inform you that your support ticket has been resolved.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Ticket Details:</strong></p>
                    <p style="margin: 5px 0;">Ticket Number: {{ticket.id}}</p>
                    <p style="margin: 5px 0;">Subject: {{ticket.subject}}</p>
                    <p style="margin: 5px 0;">Date Submitted: {{ticket.date}}</p>
                    <p style="margin: 5px 0;">Date Resolved: {{ticket.resolved_date}}</p>
                    <p style="margin: 5px 0;">Status: {{ticket.status}}</p>
                </div>
                
                <p>Resolution Summary:</p>
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;">{{ticket.resolution}}</p>
                </div>
                
                <p>If you feel that your issue hasn\'t been fully resolved, or if you have any additional questions, you can reopen your ticket:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{ticket.url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Ticket</a>
                </p>
                
                <p>We\'d appreciate your feedback on your support experience:</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{ticket.feedback_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Rate Your Experience</a>
                </p>
                
                <p>Thank you for giving us the opportunity to assist you.</p>
                <p>Sincerely,<br>The {{site.name}} Support Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getCustomerFeedbackTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #4f46e5; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Thank You for Your Feedback</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello {{user.name}},</p>
                <p>Thank you for taking the time to share your feedback with us. We truly value your input as it helps us improve our products and services.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Feedback Summary:</strong></p>
                    <p style="margin: 5px 0;">Date Submitted: {{feedback.date}}</p>
                    <p style="margin: 5px 0;">Category: {{feedback.category}}</p>
                    <p style="margin: 5px 0;">Reference Number: {{feedback.id}}</p>
                </div>
                
                <p>Your feedback has been forwarded to the appropriate team for review. If you\'ve requested a response or if we need additional information, a member of our team will contact you shortly.</p>
                
                <p>We\'re committed to continuously improving your experience with us, and your feedback plays a crucial role in that process.</p>
                
                <p>Thank you again for helping us serve you better.</p>
                <p>Sincerely,<br>The {{site.name}} Team</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.url}}" style="color: #4f46e5; text-decoration: none;">Visit Our Website</a> | 
                    <a href="{{site.url}}/contact" style="color: #4f46e5; text-decoration: none;">Contact Us</a>
                </p>
            </div>
        </div>
        ';
    }

    // 🛠️ Admin / Internal Templates
    private function getLowStockAlertTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #ef4444; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Low Stock Alert</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello Admin,</p>
                <p>This is an automated notification to inform you that the following product has reached its low stock threshold:</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Product Details:</strong></p>
                    <p style="margin: 5px 0;">Product Name: {{product.name}}</p>
                    <p style="margin: 5px 0;">SKU: {{product.sku}}</p>
                    <p style="margin: 5px 0;">Current Stock: {{product.current_stock}}</p>
                    <p style="margin: 5px 0;">Low Stock Threshold: {{product.threshold}}</p>
                    <p style="margin: 5px 0;">Vendor: {{product.vendor}}</p>
                </div>
                
                <p>Recent Sales History:</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Period</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Units Sold</th>
                    </tr>
                    {{#each product.sales_history}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.period}}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{this.units}}</td>
                    </tr>
                    {{/each}}
                </table>
                
                <p>Please take appropriate action to restock this item.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{product.admin_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Product</a>
                    <a href="{{product.reorder_url}}" style="background-color: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-left: 10px;">Reorder</a>
                </p>
                
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.admin_url}}" style="color: #4f46e5; text-decoration: none;">Admin Dashboard</a> | 
                    <a href="{{site.admin_url}}/inventory" style="color: #4f46e5; text-decoration: none;">Inventory Management</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getNewOrderPlacedTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #10b981; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">New Order Placed</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello Admin,</p>
                <p>A new order has been placed on your store.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Order Details:</strong></p>
                    <p style="margin: 5px 0;">Order Number: {{order.id}}</p>
                    <p style="margin: 5px 0;">Date: {{order.date}}</p>
                    <p style="margin: 5px 0;">Customer: {{order.customer_name}} ({{order.customer_email}})</p>
                    <p style="margin: 5px 0;">Total: {{order.total}}</p>
                    <p style="margin: 5px 0;">Payment Method: {{order.payment_method}}</p>
                    <p style="margin: 5px 0;">Shipping Method: {{order.shipping_method}}</p>
                </div>
                
                <p>Order Items:</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Product</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">Quantity</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Price</th>
                    </tr>
                    {{#each order.items}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.name}} (SKU: {{this.sku}})</td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">{{this.quantity}}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{this.price}}</td>
                    </tr>
                    {{/each}}
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;" colspan="2">Subtotal</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.subtotal}}</td>
                    </tr>
                    <tr>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;" colspan="2">Shipping</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.shipping_cost}}</td>
                    </tr>
                    <tr>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;" colspan="2">Tax</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{order.tax}}</td>
                    </tr>
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;" colspan="2">Total</th>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb; font-weight: bold;">{{order.total}}</td>
                    </tr>
                </table>
                
                <div style="display: flex; justify-content: space-between; margin: 20px 0;">
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Shipping Address:</p>
                        <p style="margin: 5px 0;">{{order.shipping_address.name}}</p>
                        <p style="margin: 5px 0;">{{order.shipping_address.street}}</p>
                        <p style="margin: 5px 0;">{{order.shipping_address.city}}, {{order.shipping_address.state}} {{order.shipping_address.zip}}</p>
                        <p style="margin: 5px 0;">{{order.shipping_address.country}}</p>
                        <p style="margin: 5px 0;">{{order.shipping_address.phone}}</p>
                    </div>
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Billing Address:</p>
                        <p style="margin: 5px 0;">{{order.billing_address.name}}</p>
                        <p style="margin: 5px 0;">{{order.billing_address.street}}</p>
                        <p style="margin: 5px 0;">{{order.billing_address.city}}, {{order.billing_address.state}} {{order.billing_address.zip}}</p>
                        <p style="margin: 5px 0;">{{order.billing_address.country}}</p>
                        <p style="margin: 5px 0;">{{order.billing_address.phone}}</p>
                    </div>
                </div>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{order.admin_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Order</a>
                    <a href="{{order.process_url}}" style="background-color: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-left: 10px;">Process Order</a>
                </p>
                
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.admin_url}}" style="color: #4f46e5; text-decoration: none;">Admin Dashboard</a> | 
                    <a href="{{site.admin_url}}/orders" style="color: #4f46e5; text-decoration: none;">Order Management</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getNewUserSignupTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #10b981; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">New User Signup</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello Admin,</p>
                <p>A new user has signed up on your website.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>User Details:</strong></p>
                    <p style="margin: 5px 0;">Name: {{user.name}}</p>
                    <p style="margin: 5px 0;">Email: {{user.email}}</p>
                    <p style="margin: 5px 0;">Signup Date: {{user.signup_date}}</p>
                    <p style="margin: 5px 0;">IP Address: {{user.ip_address}}</p>
                    <p style="margin: 5px 0;">Referral Source: {{user.referral_source}}</p>
                </div>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{user.admin_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View User Profile</a>
                </p>
                
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.admin_url}}" style="color: #4f46e5; text-decoration: none;">Admin Dashboard</a> | 
                    <a href="{{site.admin_url}}/users" style="color: #4f46e5; text-decoration: none;">User Management</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getFailedCronJobTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #ef4444; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">Cron Job Failure Alert</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello Admin,</p>
                <p>This is an automated notification to inform you that a scheduled cron job has failed.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Cron Job Details:</strong></p>
                    <p style="margin: 5px 0;">Job Name: {{cron.name}}</p>
                    <p style="margin: 5px 0;">Scheduled Time: {{cron.scheduled_time}}</p>
                    <p style="margin: 5px 0;">Failure Time: {{cron.failure_time}}</p>
                    <p style="margin: 5px 0;">Server: {{cron.server}}</p>
                </div>
                
                <p>Error Information:</p>
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0; font-family: monospace; white-space: pre-wrap;">
                    {{cron.error_message}}
                </div>
                
                <p>Recent Job History:</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Run Time</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">Status</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Duration</th>
                    </tr>
                    {{#each cron.history}}
                    <tr>
                        <td style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">{{this.time}}</td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb; color: {{this.status_color}};">{{this.status}}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">{{this.duration}}</td>
                    </tr>
                    {{/each}}
                </table>
                
                <p>Please investigate this issue as soon as possible to prevent any disruption to your system\'s operations.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{cron.logs_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Logs</a>
                    <a href="{{cron.run_url}}" style="background-color: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-left: 10px;">Run Manually</a>
                </p>
                
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.admin_url}}" style="color: #4f46e5; text-decoration: none;">Admin Dashboard</a> | 
                    <a href="{{site.admin_url}}/system/cron" style="color: #4f46e5; text-decoration: none;">Cron Job Management</a>
                </p>
            </div>
        </div>
        ';
    }

    private function getSystemErrorTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #ef4444; padding: 20px; text-align: center;">
                <h1 style="color: white; margin: 0;">System Error Alert</h1>
            </div>
            <div style="padding: 20px;">
                <p>Hello Admin,</p>
                <p>This is an automated notification to inform you that a system error has occurred.</p>
                
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0;"><strong>Error Details:</strong></p>
                    <p style="margin: 5px 0;">Error Type: {{error.type}}</p>
                    <p style="margin: 5px 0;">Timestamp: {{error.timestamp}}</p>
                    <p style="margin: 5px 0;">Environment: {{error.environment}}</p>
                    <p style="margin: 5px 0;">Server: {{error.server}}</p>
                    <p style="margin: 5px 0;">URL: {{error.url}}</p>
                    <p style="margin: 5px 0;">User: {{error.user}}</p>
                </div>
                
                <p>Error Message:</p>
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0; font-family: monospace; white-space: pre-wrap;">
                    {{error.message}}
                </div>
                
                <p>Stack Trace:</p>
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0; font-family: monospace; white-space: pre-wrap; max-height: 200px; overflow-y: auto;">
                    {{error.stack_trace}}
                </div>
                
                <p>Please investigate this issue as soon as possible.</p>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="{{error.logs_url}}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View Logs</a>
                </p>
                
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
            <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                <p>&copy; {{current_year}} {{site.name}}. All rights reserved.</p>
                <p>
                    <a href="{{site.admin_url}}" style="color: #4f46e5; text-decoration: none;">Admin Dashboard</a> | 
                    <a href="{{site.admin_url}}/system/logs" style="color: #4f46e5; text-decoration: none;">System Logs</a>
                </p>
            </div>
        </div>
        ';
    }
}
