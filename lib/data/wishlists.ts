export const wishlists = [
  {
    id: "1",
    user_id: "1",
    user: {
      id: "1",
      name: "John Doe",
      email: "john@example.com",
    },
    name: "My Wishlist",
    is_public: true,
    description: "Products I want to buy soon",
    created_at: "2023-05-15T10:00:00Z",
    updated_at: "2023-05-15T10:00:00Z",
    items: [
      {
        id: "1",
        wishlist_id: "1",
        product_id: "1",
        product: {
          id: "1",
          name: "Smartphone X",
          price: 799.99,
          thumbnail: "/modern-smartphone.png",
        },
        notes: "I need a new phone",
        created_at: "2023-05-15T10:05:00Z",
      },
      {
        id: "2",
        wishlist_id: "1",
        product_id: "3",
        product: {
          id: "3",
          name: "Wireless Headphones",
          price: 149.99,
          thumbnail: "/diverse-people-listening-headphones.png",
        },
        notes: null,
        created_at: "2023-05-15T10:10:00Z",
      },
    ],
  },
  {
    id: "2",
    user_id: "1",
    user: {
      id: "1",
      name: "John Doe",
      email: "john@example.com",
    },
    name: "Gift Ideas",
    is_public: false,
    description: "Gift ideas for family and friends",
    created_at: "2023-06-20T14:30:00Z",
    updated_at: "2023-06-20T14:30:00Z",
    items: [
      {
        id: "3",
        wishlist_id: "2",
        product_id: "5",
        product: {
          id: "5",
          name: "Smart Watch",
          price: 249.99,
          thumbnail: "/silver-ipad-on-wooden-desk.png",
        },
        notes: "For dad's birthday",
        created_at: "2023-06-20T14:35:00Z",
      },
    ],
  },
  {
    id: "3",
    user_id: "2",
    user: {
      id: "2",
      name: "Jane Smith",
      email: "jane@example.com",
    },
    name: "Tech Gadgets",
    is_public: true,
    description: "Cool tech gadgets I'm interested in",
    created_
