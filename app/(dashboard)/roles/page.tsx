"use client"

import { useState } from "react"
import { Edit, Plus, Trash2 } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Checkbox } from "@/components/ui/checkbox"
import { RoleDialog } from "@/components/roles/role-dialog"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { useToast } from "@/components/ui/use-toast"
import { roles } from "@/lib/data/roles"
import { permissions } from "@/lib/data/permissions"

export default function RolesPage() {
  const { toast } = useToast()
  const [showRoleDialog, setShowRoleDialog] = useState(false)
  const [showDeleteDialog, setShowDeleteDialog] = useState(false)
  const [editingRole, setEditingRole] = useState<any>(null)
  const [roleToDelete, setRoleToDelete] = useState<string | null>(null)
  const [permissionsState, setPermissionsState] = useState(permissions)

  const handleEditRole = (role: any) => {
    setEditingRole(role)
    setShowRoleDialog(true)
  }

  const handleDeleteRole = (roleId: string) => {
    setRoleToDelete(roleId)
    setShowDeleteDialog(true)
  }

  const confirmDelete = () => {
    // Delete role logic here
    toast({
      title: "Role deleted",
      description: "The role has been deleted successfully.",
    })
    setShowDeleteDialog(false)
    setRoleToDelete(null)
  }

  const handleSaveRole = (role: any) => {
    // Save role logic here
    toast({
      title: "Role saved",
      description: "The role has been saved successfully.",
    })
    setShowRoleDialog(false)
    setEditingRole(null)
  }

  const togglePermission = (moduleId: string, roleId: string, action: "view" | "create" | "update" | "delete") => {
    setPermissionsState(
      permissionsState.map((module) => {
        if (module.id === moduleId) {
          return {
            ...module,
            rolePermissions: module.rolePermissions.map((rp) => {
              if (rp.roleId === roleId) {
                return {
                  ...rp,
                  [action]: !rp[action],
                }
              }
              return rp
            }),
          }
        }
        return module
      }),
    )
  }

  const hasPermission = (moduleId: string, roleId: string, action: "view" | "create" | "update" | "delete") => {
    const module = permissionsState.find((m) => m.id === moduleId)
    if (!module) return false

    const rolePermission = module.rolePermissions.find((rp) => rp.roleId === roleId)
    if (!rolePermission) return false

    return rolePermission[action]
  }

  const savePermissions = () => {
    // Save permissions logic here
    toast({
      title: "Permissions saved",
      description: "The permissions have been saved successfully.",
    })
  }

  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Roles & Permissions</h2>
        <Button
          onClick={() => {
            setEditingRole(null)
            setShowRoleDialog(true)
          }}
          className="h-9 gap-1"
        >
          <Plus className="h-4 w-4" />
          <span>Add Role</span>
        </Button>
      </div>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>Roles</CardTitle>
          <CardDescription>Manage user roles</CardDescription>
        </CardHeader>
        <CardContent className="p-0">
          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Description</TableHead>
                  <TableHead>Users</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {roles.map((role) => (
                  <TableRow key={role.id}>
                    <TableCell className="font-medium">{role.name}</TableCell>
                    <TableCell>{role.description}</TableCell>
                    <TableCell>{role.usersCount}</TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        <Button variant="ghost" size="icon" onClick={() => handleEditRole(role)}>
                          <Edit className="h-4 w-4" />
                          <span className="sr-only">Edit</span>
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                          onClick={() => handleDeleteRole(role.id)}
                          disabled={role.name === "Admin"}
                        >
                          <Trash2 className={`h-4 w-4 ${role.name === "Admin" ? "opacity-50" : ""}`} />
                          <span className="sr-only">Delete</span>
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>Permissions Matrix</CardTitle>
          <CardDescription>Configure permissions for each role</CardDescription>
        </CardHeader>
        <CardContent className="p-0">
          <div className="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Module</TableHead>
                  {roles.map((role) => (
                    <TableHead key={role.id} className="text-center">
                      {role.name}
                    </TableHead>
                  ))}
                </TableRow>
              </TableHeader>
              <TableBody>
                {permissionsState.map((module) => (
                  <TableRow key={module.id}>
                    <TableCell className="font-medium">{module.module}</TableCell>
                    {roles.map((role) => (
                      <TableCell key={role.id} className="text-center">
                        <div className="flex flex-col items-center space-y-2">
                          <div className="flex items-center">
                            <Checkbox
                              id={`${module.id}-${role.id}-view`}
                              checked={hasPermission(module.id, role.id, "view")}
                              onCheckedChange={() => togglePermission(module.id, role.id, "view")}
                              disabled={role.name === "Admin"}
                            />
                            <label
                              htmlFor={`${module.id}-${role.id}-view`}
                              className="ml-2 text-xs text-gray-500 dark:text-gray-400"
                            >
                              View
                            </label>
                          </div>
                          <div className="flex items-center">
                            <Checkbox
                              id={`${module.id}-${role.id}-create`}
                              checked={hasPermission(module.id, role.id, "create")}
                              onCheckedChange={() => togglePermission(module.id, role.id, "create")}
                              disabled={role.name === "Admin"}
                            />
                            <label
                              htmlFor={`${module.id}-${role.id}-create`}
                              className="ml-2 text-xs text-gray-500 dark:text-gray-400"
                            >
                              Create
                            </label>
                          </div>
                          <div className="flex items-center">
                            <Checkbox
                              id={`${module.id}-${role.id}-update`}
                              checked={hasPermission(module.id, role.id, "update")}
                              onCheckedChange={() => togglePermission(module.id, role.id, "update")}
                              disabled={role.name === "Admin"}
                            />
                            <label
                              htmlFor={`${module.id}-${role.id}-update`}
                              className="ml-2 text-xs text-gray-500 dark:text-gray-400"
                            >
                              Update
                            </label>
                          </div>
                          <div className="flex items-center">
                            <Checkbox
                              id={`${module.id}-${role.id}-delete`}
                              checked={hasPermission(module.id, role.id, "delete")}
                              onCheckedChange={() => togglePermission(module.id, role.id, "delete")}
                              disabled={role.name === "Admin"}
                            />
                            <label
                              htmlFor={`${module.id}-${role.id}-delete`}
                              className="ml-2 text-xs text-gray-500 dark:text-gray-400"
                            >
                              Delete
                            </label>
                          </div>
                        </div>
                      </TableCell>
                    ))}
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
          <div className="p-4 border-t">
            <div className="flex justify-end">
              <Button onClick={savePermissions}>Save Permissions</Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <RoleDialog open={showRoleDialog} onOpenChange={setShowRoleDialog} role={editingRole} onSave={handleSaveRole} />

      <DeleteDialog
        open={showDeleteDialog}
        onOpenChange={setShowDeleteDialog}
        title="Delete Role"
        description="Are you sure you want to delete this role? All users with this role will lose their permissions."
        onConfirm={confirmDelete}
      />
    </div>
  )
}
