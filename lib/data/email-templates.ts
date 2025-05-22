export const emailTemplates = [
  {
    id: "1",
    name: "Welcome Email",
    subject: "Welcome to ModernCommerce, {{user.name}}!",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">Welcome to ModernCommerce!</h1>
        <p>Hello {{user.name}},</p>
        <p>Thank you for creating an account with us. We're excited to have you join our community!</p>
        <p>With your new account, you can:</p>
        <ul>
          <li>Shop our latest products</li>
          <li>Track your orders</li>
          <li>Save items to your wishlist</li>
          <li>Receive exclusive offers</li>
        </ul>
        <p>If you have any questions, please don't hesitate to contact our support team.</p>
        <p>Happy shopping!</p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "transactional",
    module: "user",
    triggeredBy: "user.register",
    isActive: true,
  },
  {
    id: "2",
    name: "Order Confirmation",
    subject: "Your Order #{{order.id}} has been confirmed",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">Order Confirmed!</h1>
        <p>Hello {{user.name}},</p>
        <p>Thank you for your order. We're pleased to confirm that we've received your order #{{order.id}}.</p>
        <p>We'll send you another email when your order ships.</p>
        <p>Thank you for shopping with ModernCommerce!</p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "transactional",
    module: "order",
    triggeredBy: "order.created",
    isActive: true,
  },
  {
    id: "3",
    name: "Password Reset",
    subject: "Reset Your ModernCommerce Password",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">Password Reset Request</h1>
        <p>Hello {{user.name}},</p>
        <p>We received a request to reset your password. If you didn't make this request, you can safely ignore this email.</p>
        <p>To reset your password, click the button below:</p>
        <p style="text-align: center;">
          <a href="#" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a>
        </p>
        <p>This link will expire in 24 hours.</p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "transactional",
    module: "user",
    triggeredBy: "user.password_reset",
    isActive: true,
  },
  {
    id: "4",
    name: "Abandoned Cart Reminder",
    subject: "You left items in your cart!",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">Don't Miss Out!</h1>
        <p>Hello {{user.name}},</p>
        <p>We noticed you left some items in your shopping cart. Your cart is saved, and you can complete your purchase anytime.</p>
        <p>Here's what you left behind:</p>
        <p>{{product.name}} and other items</p>
        <p style="text-align: center;">
          <a href="#" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Complete Your Purchase</a>
        </p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "marketing",
    module: "cart",
    triggeredBy: "cart.abandoned",
    isActive: true,
  },
  {
    id: "5",
    name: "Order Shipped",
    subject: "Your Order #{{order.id}} has been shipped",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">Your Order is on the Way!</h1>
        <p>Hello {{user.name}},</p>
        <p>Great news! Your order #{{order.id}} has been shipped and is on its way to you.</p>
        <p>You can track your shipment using the tracking number below:</p>
        <p style="font-weight: bold; font-size: 18px;">TRK123456789</p>
        <p style="text-align: center;">
          <a href="#" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Track Your Order</a>
        </p>
        <p>Thank you for shopping with ModernCommerce!</p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "transactional",
    module: "order",
    triggeredBy: "order.shipped",
    isActive: true,
  },
  {
    id: "6",
    name: "New Product Announcement",
    subject: "New Arrival: {{product.name}} Now Available!",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">New Product Alert!</h1>
        <p>Hello {{user.name}},</p>
        <p>We're excited to announce that {{product.name}} is now available in our store!</p>
        <p>Be among the first to get this amazing new product.</p>
        <p style="text-align: center;">
          <a href="#" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Shop Now</a>
        </p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "marketing",
    module: "product",
    triggeredBy: "product.new",
    isActive: false,
  },
  {
    id: "7",
    name: "Review Request",
    subject: "How was your experience with {{product.name}}?",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">We Value Your Feedback!</h1>
        <p>Hello {{user.name}},</p>
        <p>Thank you for your recent purchase of {{product.name}}. We hope you're enjoying it!</p>
        <p>We'd love to hear your thoughts. Would you mind taking a moment to leave a review?</p>
        <p style="text-align: center;">
          <a href="#" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Write a Review</a>
        </p>
        <p>Your feedback helps us improve and assists other customers in making informed decisions.</p>
        <p>Thank you for your support!</p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "marketing",
    module: "review",
    triggeredBy: "review.request",
    isActive: true,
  },
  {
    id: "8",
    name: "Coupon Code",
    subject: "Special Offer: 20% Off Your Next Purchase!",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">Special Discount Just for You!</h1>
        <p>Hello {{user.name}},</p>
        <p>As a valued customer, we're offering you a special 20% discount on your next purchase!</p>
        <p>Use the coupon code below at checkout:</p>
        <p style="font-weight: bold; font-size: 24px; text-align: center; padding: 10px; background-color: #f3f4f6; border-radius: 5px;">SAVE20NOW</p>
        <p>This offer expires in 7 days, so don't miss out!</p>
        <p style="text-align: center;">
          <a href="#" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Shop Now</a>
        </p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "marketing",
    module: "coupon",
    triggeredBy: "coupon.new",
    isActive: true,
  },
  {
    id: "9",
    name: "Order Delivered",
    subject: "Your Order #{{order.id}} has been delivered",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">Your Order Has Been Delivered!</h1>
        <p>Hello {{user.name}},</p>
        <p>We're happy to inform you that your order #{{order.id}} has been delivered!</p>
        <p>We hope you enjoy your purchase. If you have any questions or concerns, please don't hesitate to contact our customer service team.</p>
        <p>Thank you for shopping with ModernCommerce!</p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "transactional",
    module: "order",
    triggeredBy: "order.delivered",
    isActive: true,
  },
  {
    id: "10",
    name: "Payment Failed",
    subject: "Payment Failed for Order #{{order.id}}",
    body: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #4f46e5;">Payment Issue Detected</h1>
        <p>Hello {{user.name}},</p>
        <p>We're sorry to inform you that we couldn't process the payment for your order #{{order.id}}.</p>
        <p>This could be due to insufficient funds, an expired card, or incorrect payment details.</p>
        <p>Please update your payment information to complete your purchase:</p>
        <p style="text-align: center;">
          <a href="#" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Update Payment</a>
        </p>
        <p>If you need assistance, please contact our customer support team.</p>
        <p>The ModernCommerce Team</p>
      </div>
    `,
    type: "transactional",
    module: "payment",
    triggeredBy: "payment.failed",
    isActive: true,
  },
]
