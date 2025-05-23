"use client"

import { useState, useEffect } from "react"
import { zodResolver } from "@hookform/resolvers/zod"
import { useForm } from "react-hook-form"
import * as z from "zod"
import { Check, Palette, Save } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form"
import { Input } from "@/components/ui/input"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Switch } from "@/components/ui/switch"
import { useToast } from "@/components/ui/use-toast"
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group"
import { useColorScheme } from "@/contexts/color-scheme-context"

const generalFormSchema = z.object({
  storeName: z.string().min(2, {
    message: "Store name must be at least 2 characters.",
  }),
  storeEmail: z.string().email({
    message: "Please enter a valid email address.",
  }),
  storePhone: z.string().min(10, {
    message: "Please enter a valid phone number.",
  }),
  storeCurrency: z.string({
    required_error: "Please select a currency.",
  }),
  storeAddress: z.string().min(5, {
    message: "Address must be at least 5 characters.",
  }),
  storeCity: z.string().min(2, {
    message: "City must be at least 2 characters.",
  }),
  storeState: z.string().min(2, {
    message: "State must be at least 2 characters.",
  }),
  storeZip: z.string().min(5, {
    message: "ZIP code must be at least 5 characters.",
  }),
  storeCountry: z.string({
    required_error: "Please select a country.",
  }),
})

const emailFormSchema = z.object({
  smtpHost: z.string().min(1, {
    message: "SMTP host is required.",
  }),
  smtpPort: z.string().min(1, {
    message: "SMTP port is required.",
  }),
  smtpUser: z.string().min(1, {
    message: "SMTP username is required.",
  }),
  smtpPassword: z.string().min(1, {
    message: "SMTP password is required.",
  }),
  smtpEncryption: z.string({
    required_error: "Please select an encryption type.",
  }),
  senderName: z.string().min(1, {
    message: "Sender name is required.",
  }),
  senderEmail: z.string().email({
    message: "Please enter a valid email address.",
  }),
})

const paymentFormSchema = z.object({
  stripeEnabled: z.boolean().default(false),
  stripeKey: z.string().optional(),
  stripeSecret: z.string().optional(),
  paypalEnabled: z.boolean().default(false),
  paypalClientId: z.string().optional(),
  paypalSecret: z.string().optional(),
  codEnabled: z.boolean().default(true),
})

const notificationFormSchema = z.object({
  orderCreated: z.boolean().default(true),
  orderStatusChanged: z.boolean().default(true),
  orderShipped: z.boolean().default(true),
  orderDelivered: z.boolean().default(true),
  lowStock: z.boolean().default(true),
  newUser: z.boolean().default(true),
  newComment: z.boolean().default(true),
})

const appearanceFormSchema = z.object({
  colorScheme: z.string().default("default"),
})

export default function SettingsPage() {
  const { toast } = useToast()
  const [activeTab, setActiveTab] = useState("general")
  const { colorScheme, setColorScheme } = useColorScheme()

  const generalForm = useForm<z.infer<typeof generalFormSchema>>({
    resolver: zodResolver(generalFormSchema),
    defaultValues: {
      storeName: "Modern eCommerce",
      storeEmail: "info@modernecommerce.com",
      storePhone: "+1 (555) 123-4567",
      storeCurrency: "USD",
      storeAddress: "123 Commerce St",
      storeCity: "New York",
      storeState: "NY",
      storeZip: "10001",
      storeCountry: "US",
    },
  })

  const emailForm = useForm<z.infer<typeof emailFormSchema>>({
    resolver: zodResolver(emailFormSchema),
    defaultValues: {
      smtpHost: "smtp.example.com",
      smtpPort: "587",
      smtpUser: "user@example.com",
      smtpPassword: "password",
      smtpEncryption: "tls",
      senderName: "Modern eCommerce",
      senderEmail: "noreply@modernecommerce.com",
    },
  })

  const paymentForm = useForm<z.infer<typeof paymentFormSchema>>({
    resolver: zodResolver(paymentFormSchema),
    defaultValues: {
      stripeEnabled: true,
      stripeKey: "pk_test_123456789",
      stripeSecret: "sk_test_123456789",
      paypalEnabled: true,
      paypalClientId: "client_id_123456789",
      paypalSecret: "client_secret_123456789",
      codEnabled: true,
    },
  })

  const notificationForm = useForm<z.infer<typeof notificationFormSchema>>({
    resolver: zodResolver(notificationFormSchema),
    defaultValues: {
      orderCreated: true,
      orderStatusChanged: true,
      orderShipped: true,
      orderDelivered: true,
      lowStock: true,
      newUser: true,
      newComment: true,
    },
  })

  const appearanceForm = useForm<z.infer<typeof appearanceFormSchema>>({
    resolver: zodResolver(appearanceFormSchema),
    defaultValues: {
      colorScheme: colorScheme,
    },
  })

  // Update form when colorScheme changes
  useEffect(() => {
    appearanceForm.setValue("colorScheme", colorScheme)
  }, [colorScheme, appearanceForm])

  function onGeneralSubmit(values: z.infer<typeof generalFormSchema>) {
    toast({
      title: "General settings saved",
      description: "Your general settings have been saved successfully.",
    })
  }

  function onEmailSubmit(values: z.infer<typeof emailFormSchema>) {
    toast({
      title: "Email settings saved",
      description: "Your email settings have been saved successfully.",
    })
  }

  function onPaymentSubmit(values: z.infer<typeof paymentFormSchema>) {
    toast({
      title: "Payment settings saved",
      description: "Your payment settings have been saved successfully.",
    })
  }

  function onNotificationSubmit(values: z.infer<typeof notificationFormSchema>) {
    toast({
      title: "Notification settings saved",
      description: "Your notification settings have been saved successfully.",
    })
  }

  function onAppearanceSubmit(values: z.infer<typeof appearanceFormSchema>) {
    setColorScheme(values.colorScheme as any)
    toast({
      title: "Appearance settings saved",
      description: "Your appearance settings have been saved successfully.",
    })
  }

  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Settings</h2>
      </div>

      <Tabs defaultValue="general" value={activeTab} onValueChange={setActiveTab} className="space-y-4">
        <TabsList className="grid w-full grid-cols-5">
          <TabsTrigger value="general">General</TabsTrigger>
          <TabsTrigger value="email">Email</TabsTrigger>
          <TabsTrigger value="payment">Payment</TabsTrigger>
          <TabsTrigger value="notifications">Notifications</TabsTrigger>
          <TabsTrigger value="appearance">Appearance</TabsTrigger>
        </TabsList>

        <TabsContent value="general" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>General Settings</CardTitle>
              <CardDescription>Manage your store&apos;s general settings</CardDescription>
            </CardHeader>
            <CardContent>
              <Form {...generalForm}>
                <form onSubmit={generalForm.handleSubmit(onGeneralSubmit)} className="space-y-8">
                  <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <FormField
                      control={generalForm.control}
                      name="storeName"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Store Name</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormDescription>The name of your store</FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={generalForm.control}
                      name="storeEmail"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Store Email</FormLabel>
                          <FormControl>
                            <Input {...field} type="email" />
                          </FormControl>
                          <FormDescription>The primary email address for your store</FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={generalForm.control}
                      name="storePhone"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Store Phone</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormDescription>The primary phone number for your store</FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={generalForm.control}
                      name="storeCurrency"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Currency</FormLabel>
                          <Select onValueChange={field.onChange} defaultValue={field.value}>
                            <FormControl>
                              <SelectTrigger>
                                <SelectValue placeholder="Select a currency" />
                              </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                              <SelectItem value="USD">USD - US Dollar</SelectItem>
                              <SelectItem value="EUR">EUR - Euro</SelectItem>
                              <SelectItem value="GBP">GBP - British Pound</SelectItem>
                              <SelectItem value="JPY">JPY - Japanese Yen</SelectItem>
                              <SelectItem value="CAD">CAD - Canadian Dollar</SelectItem>
                            </SelectContent>
                          </Select>
                          <FormDescription>The currency used for your store</FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  <div>
                    <h3 className="text-lg font-medium">Store Address</h3>
                    <div className="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                      <FormField
                        control={generalForm.control}
                        name="storeAddress"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Address</FormLabel>
                            <FormControl>
                              <Input {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={generalForm.control}
                        name="storeCity"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>City</FormLabel>
                            <FormControl>
                              <Input {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={generalForm.control}
                        name="storeState"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>State / Province</FormLabel>
                            <FormControl>
                              <Input {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={generalForm.control}
                        name="storeZip"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>ZIP / Postal Code</FormLabel>
                            <FormControl>
                              <Input {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={generalForm.control}
                        name="storeCountry"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Country</FormLabel>
                            <Select onValueChange={field.onChange} defaultValue={field.value}>
                              <FormControl>
                                <SelectTrigger>
                                  <SelectValue placeholder="Select a country" />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                <SelectItem value="US">United States</SelectItem>
                                <SelectItem value="CA">Canada</SelectItem>
                                <SelectItem value="GB">United Kingdom</SelectItem>
                                <SelectItem value="AU">Australia</SelectItem>
                                <SelectItem value="DE">Germany</SelectItem>
                                <SelectItem value="FR">France</SelectItem>
                              </SelectContent>
                            </Select>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>
                  </div>

                  <Button type="submit" className="gap-1">
                    <Save className="h-4 w-4" />
                    Save Changes
                  </Button>
                </form>
              </Form>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="email" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Email Settings</CardTitle>
              <CardDescription>Configure your store&apos;s email settings</CardDescription>
            </CardHeader>
            <CardContent>
              <Form {...emailForm}>
                <form onSubmit={emailForm.handleSubmit(onEmailSubmit)} className="space-y-8">
                  <div>
                    <h3 className="text-lg font-medium">SMTP Configuration</h3>
                    <div className="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                      <FormField
                        control={emailForm.control}
                        name="smtpHost"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>SMTP Host</FormLabel>
                            <FormControl>
                              <Input {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={emailForm.control}
                        name="smtpPort"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>SMTP Port</FormLabel>
                            <FormControl>
                              <Input {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={emailForm.control}
                        name="smtpUser"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>SMTP Username</FormLabel>
                            <FormControl>
                              <Input {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={emailForm.control}
                        name="smtpPassword"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>SMTP Password</FormLabel>
                            <FormControl>
                              <Input {...field} type="password" />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={emailForm.control}
                        name="smtpEncryption"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Encryption</FormLabel>
                            <Select onValueChange={field.onChange} defaultValue={field.value}>
                              <FormControl>
                                <SelectTrigger>
                                  <SelectValue placeholder="Select encryption type" />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                <SelectItem value="none">None</SelectItem>
                                <SelectItem value="ssl">SSL</SelectItem>
                                <SelectItem value="tls">TLS</SelectItem>
                              </SelectContent>
                            </Select>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>
                  </div>

                  <div>
                    <h3 className="text-lg font-medium">Sender Information</h3>
                    <div className="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                      <FormField
                        control={emailForm.control}
                        name="senderName"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Sender Name</FormLabel>
                            <FormControl>
                              <Input {...field} />
                            </FormControl>
                            <FormDescription>The name that will appear in the from field</FormDescription>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={emailForm.control}
                        name="senderEmail"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Sender Email</FormLabel>
                            <FormControl>
                              <Input {...field} type="email" />
                            </FormControl>
                            <FormDescription>The email address that will appear in the from field</FormDescription>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>
                  </div>

                  <Button type="submit" className="gap-1">
                    <Save className="h-4 w-4" />
                    Save Changes
                  </Button>
                </form>
              </Form>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="payment" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Payment Settings</CardTitle>
              <CardDescription>Configure your store&apos;s payment methods</CardDescription>
            </CardHeader>
            <CardContent>
              <Form {...paymentForm}>
                <form onSubmit={paymentForm.handleSubmit(onPaymentSubmit)} className="space-y-8">
                  <div>
                    <h3 className="text-lg font-medium">Stripe</h3>
                    <div className="grid grid-cols-1 gap-6 mt-4">
                      <FormField
                        control={paymentForm.control}
                        name="stripeEnabled"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">Enable Stripe</FormLabel>
                              <FormDescription>Allow customers to pay with Stripe</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />

                      {paymentForm.watch("stripeEnabled") && (
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                          <FormField
                            control={paymentForm.control}
                            name="stripeKey"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Stripe Publishable Key</FormLabel>
                                <FormControl>
                                  <Input {...field} />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />

                          <FormField
                            control={paymentForm.control}
                            name="stripeSecret"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Stripe Secret Key</FormLabel>
                                <FormControl>
                                  <Input {...field} type="password" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      )}
                    </div>
                  </div>

                  <div>
                    <h3 className="text-lg font-medium">PayPal</h3>
                    <div className="grid grid-cols-1 gap-6 mt-4">
                      <FormField
                        control={paymentForm.control}
                        name="paypalEnabled"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">Enable PayPal</FormLabel>
                              <FormDescription>Allow customers to pay with PayPal</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />

                      {paymentForm.watch("paypalEnabled") && (
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                          <FormField
                            control={paymentForm.control}
                            name="paypalClientId"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>PayPal Client ID</FormLabel>
                                <FormControl>
                                  <Input {...field} />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />

                          <FormField
                            control={paymentForm.control}
                            name="paypalSecret"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>PayPal Secret</FormLabel>
                                <FormControl>
                                  <Input {...field} type="password" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      )}
                    </div>
                  </div>

                  <div>
                    <h3 className="text-lg font-medium">Cash on Delivery</h3>
                    <div className="grid grid-cols-1 gap-6 mt-4">
                      <FormField
                        control={paymentForm.control}
                        name="codEnabled"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">Enable Cash on Delivery</FormLabel>
                              <FormDescription>Allow customers to pay when they receive their order</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />
                    </div>
                  </div>

                  <Button type="submit" className="gap-1">
                    <Save className="h-4 w-4" />
                    Save Changes
                  </Button>
                </form>
              </Form>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="notifications" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Notification Settings</CardTitle>
              <CardDescription>Configure which notifications you want to receive</CardDescription>
            </CardHeader>
            <CardContent>
              <Form {...notificationForm}>
                <form onSubmit={notificationForm.handleSubmit(onNotificationSubmit)} className="space-y-8">
                  <div>
                    <h3 className="text-lg font-medium">Order Notifications</h3>
                    <div className="grid grid-cols-1 gap-6 mt-4">
                      <FormField
                        control={notificationForm.control}
                        name="orderCreated"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">Order Created</FormLabel>
                              <FormDescription>Receive a notification when a new order is created</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={notificationForm.control}
                        name="orderStatusChanged"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">Order Status Changed</FormLabel>
                              <FormDescription>Receive a notification when an order status is changed</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={notificationForm.control}
                        name="orderShipped"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">Order Shipped</FormLabel>
                              <FormDescription>Receive a notification when an order is shipped</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={notificationForm.control}
                        name="orderDelivered"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">Order Delivered</FormLabel>
                              <FormDescription>Receive a notification when an order is delivered</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />
                    </div>
                  </div>

                  <div>
                    <h3 className="text-lg font-medium">Inventory Notifications</h3>
                    <div className="grid grid-cols-1 gap-6 mt-4">
                      <FormField
                        control={notificationForm.control}
                        name="lowStock"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">Low Stock</FormLabel>
                              <FormDescription>Receive a notification when a product is low in stock</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />
                    </div>
                  </div>

                  <div>
                    <h3 className="text-lg font-medium">User & Content Notifications</h3>
                    <div className="grid grid-cols-1 gap-6 mt-4">
                      <FormField
                        control={notificationForm.control}
                        name="newUser"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">New User Registration</FormLabel>
                              <FormDescription>Receive a notification when a new user registers</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={notificationForm.control}
                        name="newComment"
                        render={({ field }) => (
                          <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                              <FormLabel className="text-base">New Comment</FormLabel>
                              <FormDescription>Receive a notification when a new comment is posted</FormDescription>
                            </div>
                            <FormControl>
                              <Switch checked={field.value} onCheckedChange={field.onChange} />
                            </FormControl>
                          </FormItem>
                        )}
                      />
                    </div>
                  </div>

                  <Button type="submit" className="gap-1">
                    <Save className="h-4 w-4" />
                    Save Changes
                  </Button>
                </form>
              </Form>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="appearance" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Appearance Settings</CardTitle>
              <CardDescription>Customize the look and feel of your admin panel</CardDescription>
            </CardHeader>
            <CardContent>
              <Form {...appearanceForm}>
                <form onSubmit={appearanceForm.handleSubmit(onAppearanceSubmit)} className="space-y-8">
                  <div>
                    <h3 className="text-lg font-medium">Color Scheme</h3>
                    <FormField
                      control={appearanceForm.control}
                      name="colorScheme"
                      render={({ field }) => (
                        <FormItem className="space-y-4 mt-4">
                          <FormLabel>Select a color scheme for your admin panel</FormLabel>
                          <FormControl>
                            <RadioGroup
                              onValueChange={(value) => {
                                field.onChange(value)
                                // Apply immediately for preview
                                setColorScheme(value as any)
                              }}
                              value={field.value}
                              className="grid grid-cols-1 md:grid-cols-3 gap-4"
                            >
                              <FormItem className="relative">
                                <FormControl>
                                  <RadioGroupItem value="default" className="sr-only" id="default" />
                                </FormControl>
                                <FormLabel
                                  htmlFor="default"
                                  className="flex flex-col items-center justify-between rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground peer-data-[state=checked]:border-primary [&:has([data-state=checked])]:border-primary cursor-pointer"
                                >
                                  <div className="mb-4 flex h-16 w-full items-center justify-center rounded-md bg-gradient-to-r from-gray-200 to-gray-300">
                                    <Palette className="h-8 w-8 text-gray-800" />
                                  </div>
                                  <div className="font-medium">Default</div>
                                  <div className="text-xs text-muted-foreground mt-1">Classic admin theme</div>
                                  {field.value === "default" && (
                                    <div className="absolute top-3 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                      <Check className="h-3 w-3" />
                                    </div>
                                  )}
                                </FormLabel>
                              </FormItem>
                              <FormItem className="relative">
                                <FormControl>
                                  <RadioGroupItem value="ocean" className="sr-only" id="ocean" />
                                </FormControl>
                                <FormLabel
                                  htmlFor="ocean"
                                  className="flex flex-col items-center justify-between rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground peer-data-[state=checked]:border-primary [&:has([data-state=checked])]:border-primary cursor-pointer"
                                >
                                  <div className="mb-4 flex h-16 w-full items-center justify-center rounded-md bg-gradient-to-r from-blue-400 to-teal-400">
                                    <Palette className="h-8 w-8 text-white" />
                                  </div>
                                  <div className="font-medium">Ocean</div>
                                  <div className="text-xs text-muted-foreground mt-1">Calming blue & teal</div>
                                  {field.value === "ocean" && (
                                    <div className="absolute top-3 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                      <Check className="h-3 w-3" />
                                    </div>
                                  )}
                                </FormLabel>
                              </FormItem>
                              <FormItem className="relative">
                                <FormControl>
                                  <RadioGroupItem value="forest" className="sr-only" id="forest" />
                                </FormControl>
                                <FormLabel
                                  htmlFor="forest"
                                  className="flex flex-col items-center justify-between rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground peer-data-[state=checked]:border-primary [&:has([data-state=checked])]:border-primary cursor-pointer"
                                >
                                  <div className="mb-4 flex h-16 w-full items-center justify-center rounded-md bg-gradient-to-r from-green-500 to-emerald-600">
                                    <Palette className="h-8 w-8 text-white" />
                                  </div>
                                  <div className="font-medium">Forest</div>
                                  <div className="text-xs text-muted-foreground mt-1">Natural green tones</div>
                                  {field.value === "forest" && (
                                    <div className="absolute top-3 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                      <Check className="h-3 w-3" />
                                    </div>
                                  )}
                                </FormLabel>
                              </FormItem>
                              <FormItem className="relative">
                                <FormControl>
                                  <RadioGroupItem value="sunset" className="sr-only" id="sunset" />
                                </FormControl>
                                <FormLabel
                                  htmlFor="sunset"
                                  className="flex flex-col items-center justify-between rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground peer-data-[state=checked]:border-primary [&:has([data-state=checked])]:border-primary cursor-pointer"
                                >
                                  <div className="mb-4 flex h-16 w-full items-center justify-center rounded-md bg-gradient-to-r from-orange-400 to-pink-600">
                                    <Palette className="h-8 w-8 text-white" />
                                  </div>
                                  <div className="font-medium">Sunset</div>
                                  <div className="text-xs text-muted-foreground mt-1">Warm orange & purple</div>
                                  {field.value === "sunset" && (
                                    <div className="absolute top-3 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                      <Check className="h-3 w-3" />
                                    </div>
                                  )}
                                </FormLabel>
                              </FormItem>
                              <FormItem className="relative">
                                <FormControl>
                                  <RadioGroupItem value="midnight" className="sr-only" id="midnight" />
                                </FormControl>
                                <FormLabel
                                  htmlFor="midnight"
                                  className="flex flex-col items-center justify-between rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground peer-data-[state=checked]:border-primary [&:has([data-state=checked])]:border-primary cursor-pointer"
                                >
                                  <div className="mb-4 flex h-16 w-full items-center justify-center rounded-md bg-gradient-to-r from-gray-900 to-blue-900">
                                    <Palette className="h-8 w-8 text-blue-300" />
                                  </div>
                                  <div className="font-medium">Midnight</div>
                                  <div className="text-xs text-muted-foreground mt-1">Dark with blue accents</div>
                                  {field.value === "midnight" && (
                                    <div className="absolute top-3 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                      <Check className="h-3 w-3" />
                                    </div>
                                  )}
                                </FormLabel>
                              </FormItem>
                              <FormItem className="relative">
                                <FormControl>
                                  <RadioGroupItem value="lavender" className="sr-only" id="lavender" />
                                </FormControl>
                                <FormLabel
                                  htmlFor="lavender"
                                  className="flex flex-col items-center justify-between rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground peer-data-[state=checked]:border-primary [&:has([data-state=checked])]:border-primary cursor-pointer"
                                >
                                  <div className="mb-4 flex h-16 w-full items-center justify-center rounded-md bg-gradient-to-r from-purple-400 to-pink-300">
                                    <Palette className="h-8 w-8 text-white" />
                                  </div>
                                  <div className="font-medium">Lavender</div>
                                  <div className="text-xs text-muted-foreground mt-1">Soft purple & pink</div>
                                  {field.value === "lavender" && (
                                    <div className="absolute top-3 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                      <Check className="h-3 w-3" />
                                    </div>
                                  )}
                                </FormLabel>
                              </FormItem>
                            </RadioGroup>
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  <div className="border rounded-lg p-4">
                    <h3 className="text-lg font-medium mb-4">Preview</h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div className="space-y-4">
                        <div className="p-4 rounded-lg bg-card border shadow-sm">
                          <h4 className="font-medium mb-2">Card Example</h4>
                          <p className="text-sm text-muted-foreground">
                            This is how cards will appear with the selected theme.
                          </p>
                        </div>
                        <div className="flex gap-2">
                          <Button variant="default">Primary</Button>
                          <Button variant="secondary">Secondary</Button>
                          <Button variant="outline">Outline</Button>
                        </div>
                      </div>
                      <div className="space-y-4">
                        <div className="h-24 rounded-lg bg-muted flex items-center justify-center">
                          <span className="text-muted-foreground">Background Color</span>
                        </div>
                        <div className="flex flex-col gap-2">
                          <div className="flex items-center gap-2">
                            <div className="w-4 h-4 rounded-full bg-primary"></div>
                            <span className="text-sm">Primary</span>
                          </div>
                          <div className="flex items-center gap-2">
                            <div className="w-4 h-4 rounded-full bg-secondary"></div>
                            <span className="text-sm">Secondary</span>
                          </div>
                          <div className="flex items-center gap-2">
                            <div className="w-4 h-4 rounded-full bg-accent"></div>
                            <span className="text-sm">Accent</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <Button type="submit" className="gap-1">
                    <Save className="h-4 w-4" />
                    Save Changes
                  </Button>
                </form>
              </Form>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  )
}
