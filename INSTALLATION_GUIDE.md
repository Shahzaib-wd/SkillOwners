# 🚀 QUICK START GUIDE - Agency Hiring System

## ⚡ 3-Step Installation

### Step 1: Import Database Changes
```bash
mysql -u root -p skill_owners < agency_system_migration.sql
```

**OR via phpMyAdmin:**
1. Login to phpMyAdmin
2. Select `skill_owners` database
3. Click "Import" tab
4. Choose `agency_system_migration.sql`
5. Click "Go"

### Step 2: Verify Installation
Check if new tables exist:
```sql
SHOW TABLES LIKE 'agency_%';
```
Should show:
- agency_members
- agency_invitations

```sql
SHOW TABLES LIKE 'role_permissions';
```
Should show:
- role_permissions

### Step 3: Test the System
1. Login as agency user
2. Go to Dashboard
3. See new tabs: Overview | Team | Invitations | Orders
4. Click "Invite Member" and test!

---

## 🎯 What Changed?

### ✅ New Files (10 total):
- `agency_system_migration.sql` - Database schema
- `models/AgencyMember.php` - Team management
- `models/AgencyInvitation.php` - Invitation system
- `controllers/AgencyController.php` - Business logic
- `dashboard/agency/team.php` - Team page
- `dashboard/agency/invite_member.php` - Invite form
- `dashboard/agency/accept_invitation.php` - Accept page
- `dashboard/agency/remove_member.php` - Remove confirmation
- `AGENCY_SYSTEM_README.md` - Full documentation
- `INSTALLATION_GUIDE.md` - This file

### ✅ Modified Files (3 total):
- `config.php` - Added permission functions
- `dashboard/agency.php` - Enhanced with tabs & stats
- `dashboard/freelancer.php` - Added invitation alerts

### ✅ Nothing Broken:
- ✅ Buyer dashboard - unchanged
- ✅ Freelancer gigs - unchanged
- ✅ Orders system - unchanged
- ✅ Chat system - unchanged
- ✅ Authentication - unchanged

---

## 🔥 Quick Feature Test

### Test 1: Agency Invites Freelancer
1. Login as agency@example.com
2. Dashboard → Click "Invite Member"
3. Enter freelancer email: freelancer@example.com
4. Select role: Member
5. Submit → Should show success message

### Test 2: Freelancer Accepts Invitation
1. Login as freelancer@example.com
2. Dashboard → See blue alert "You have 1 pending invitation"
3. Click "View Invitation"
4. Click "Accept Invitation"
5. Success! You're now part of the agency

### Test 3: Agency Manages Team
1. Login as agency
2. Dashboard → Team tab
3. See all team members with roles
4. Change a role (if admin)
5. Remove a member (if admin)

---

## 🎨 Features at a Glance

### For Agencies:
- 📊 Dashboard with team statistics
- 👥 Team member management
- 📧 Send/track invitations
- 🔐 Role-based permissions
- 🗑️ Remove team members
- 🔄 Change member roles

### For Freelancers:
- 📬 Receive agency invitations
- ✅ Accept or decline invitations
- 👀 View which agencies you're in
- 🤝 Collaborate with agency teams

---

## 🔒 Security Built-In

- ✅ CSRF protection on all forms
- ✅ Server-side permission checks
- ✅ Prepared SQL statements
- ✅ XSS protection
- ✅ Email verification
- ✅ Token expiration (7 days)

---

## 📞 Need Help?

### Common Issues:

**Q: Can't see new tabs in agency dashboard?**  
A: Clear browser cache and refresh

**Q: Invitation not showing for freelancer?**  
A: Verify email matches exactly (case-sensitive)

**Q: Database errors?**  
A: Re-run migration SQL file

**Q: Permission denied errors?**  
A: Check user role in users table

---

## ✅ Verification Checklist

After installation, verify:

- [ ] New tables exist in database
- [ ] Agency dashboard has 4 tabs
- [ ] Can create invitation
- [ ] Freelancer sees invitation alert
- [ ] Can accept invitation
- [ ] Team member appears in list
- [ ] Can change member role (as admin)
- [ ] Can remove member (as admin)
- [ ] Permissions enforced correctly

---

## 🎉 You're Done!

The Agency Hiring System is now fully functional!

**What's Next?**
1. Create test accounts
2. Invite real team members
3. Explore role permissions
4. Read full documentation: `AGENCY_SYSTEM_README.md`

---

**Installation Time:** ~5 minutes  
**Difficulty:** Easy ⭐  
**Status:** ✅ Production Ready
