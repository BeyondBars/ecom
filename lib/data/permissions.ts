export const permissions = [
  {
    id: "1",
    module: "Dashboard",
    rolePermissions: [
      { roleId: "1", view: true, create: true, update: true, delete: true },
      { roleId: "2", view: true, create: false, update: false, delete: false },
      { roleId: "3", view: true, create: false, update: false, delete: false },
      { roleId: "4", view: true, create: false, update: false, delete: false },
      { roleId: "5", view: true, create: false, update: false, delete: false },
    ],
  },
  {
    id: "2",
    module: "Products",
    rolePermissions: [
      { roleId: "1", view: true, create: true, update: true, delete: true },
      { roleId: "2", view: true, create: true, update: true, delete: true },
      { roleId: "3", view: true, create: true, update: true, delete: false },
      { roleId: "4", view: true, create: false, update: false, delete: false },
      { roleId: "5", view: true, create: false, update: false, delete: false },
    ],
  },
  {
    id: "3",
    module: "Categories",
    rolePermissions: [
      { roleId: "1", view: true, create: true, update: true, delete: true },
      { roleId: "2", view: true, create: true, update: true, delete: true },
      { roleId: "3", view: true, create: true, update: true, delete: false },
      { roleId: "4", view: true, create: false, update: false, delete: false },
      { roleId: "5", view: true, create: false, update: false, delete: false },
    ],
  },
  {
    id: "4",
    module: "Orders",
    rolePermissions: [
      { roleId: "1", view: true, create: true, update: true, delete: true },
      { roleId: "2", view: true, create: true, update: true, delete: false },
      { roleId: "3", view: false, create: false, update: false, delete: false },
      { roleId: "4", view: true, create: false, update: true, delete: false },
      { roleId: "5", view: true, create: false, update: false, delete: false },
    ],
  },
  {
    id: "5",
    module: "Invoices",
    rolePermissions: [
      { roleId: "1", view: true, create: true, update: true, delete: true },
      { roleId: "2", view: true, create: true, update: true, delete: false },
      { roleId: "3", view: false, create: false, update: false, delete: false },
      { roleId: "4", view: true, create: true, update: true, delete: false },
      { roleId: "5", view: true, create: false, update: false, delete: false },
    ],
  },
  {
    id: "6",
    module: "Users",
    rolePermissions: [
      { roleId: "1", view: true, create: true, update: true, delete: true },
      { roleId: "2", view: true, create: false, update: false, delete: false },
      { roleId: "3", view: false, create: false, update: false, delete: false },
      { roleId: "4", view: true, create: false, update: false, delete: false },
      { roleId: "5", view: false, create: false, update: false, delete: false },
    ],
  },
  {
    id: "7",
    module: "Blog",
    rolePermissions: [
      { roleId: "1", view: true, create: true, update: true, delete: true },
      { roleId: "2", view: true, create: false, update: false, delete: false },
      { roleId: "3", view: true, create: true, update: true, delete: true },
      { roleId: "4", view: true, create: false, update: false, delete: false },
      { roleId: "5", view: true, create: false, update: false, delete: false },
    ],
  },
  {
    id: "8",
    module: "Comments",
    rolePermissions: [
      { roleId: "1", view: true, create: true, update: true, delete: true },
      { roleId: "2", view: true, create: false, update: false, delete: false },
      { roleId: "3", view: true, create: true, update: true, delete: true },
      { roleId: "4", view: true, create: true, update: true, delete: true },
      { roleId: "5", view: true, create: false, update: false, delete: false },
    ],
  },
  {
    id: "9",
    module: "Settings",
    rolePermissions: [
      { roleId: "1", view: true, create: true, update: true, delete: true },
      { roleId: "2", view: true, create: false, update: true, delete: false },
      { roleId: "3", view: false, create: false, update: false, delete: false },
      { roleId: "4", view: false, create: false, update: false, delete: false },
      { roleId: "5", view: false, create: false, update: false, delete: false },
    ],
  },
]
