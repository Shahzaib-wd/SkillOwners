# 🚀 INSTALLATION & TESTING GUIDE

## Quick Start (5 Minutes)

### Option 1: Fresh Installation

```bash
# 1. Extract the project
unzip skill_owners_updated.zip
cd skill_owners

# 2. Import database
mysql -u root -p
CREATE DATABASE skill_owners CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

mysql -u root -p skill_owners < skill_owners.sql

# 3. Configure
cp .env.example .env  # Edit database credentials
nano config.php       # Update DB_NAME, DB_USER, DB_PASS

# 4. Set permissions
chmod -R 755 .
chmod -R 777 uploads/

# 5. Access
http://localhost/skill_owners
```

### Option 2: Upgrade Existing Installation

```bash
# 1. CRITICAL: Backup first!
mysqldump -u root -p skill_owners > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Run migration
mysql -u root -p skill_owners < conversation_migration.sql

# 3. Backup old files
cp -r models/ models_backup/
cp -r views/partials views/partials_backup/
cp chat.php chat.php.backup
cp chat_api.php chat_api.php.backup

# 4. Copy updated files
# Replace these files from skill_owners_updated/:
# - models/Message.php
# - chat.php
# - chat_api.php
# - views/partials/inbox.php (new)
# - dashboard/freelancer.php
# - dashboard/buyer.php
# - dashboard/agency.php

# 5. Initialize agency chats (optional but recommended)
php agency_chat_init.php

# 6. Test everything (see testing section below)
```

## 📋 Post-Installation Steps

### 1. Create Test Users (if fresh install)

```sql
-- Agency user
INSERT INTO users (email, password, full_name, role) 
VALUES ('agency@test.com', '$2y$10$abcdefghijklmnopqrstuv...', 'Test Agency', 'agency');

-- Freelancer user
INSERT INTO users (email, password, full_name, role) 
VALUES ('freelancer@test.com', '$2y$10$abcdefghijklmnopqrstuv...', 'Test Freelancer', 'freelancer');

-- Buyer user
INSERT INTO users (email, password, full_name, role) 
VALUES ('buyer@test.com', '$2y$10$abcdefghijklmnopqrstuv...', 'Test Buyer', 'buyer');

-- Add freelancer to agency
INSERT INTO agency_members (agency_id, freelancer_id, agency_role, status)
VALUES (1, 2, 'member', 'active');
```

### 2. Initialize Agency Team Chats

```bash
php agency_chat_init.php
```

Expected output:
```
Found 3 active agencies
--------------------------------------------------
Processing: Test Agency (ID: 1)... ✓ Created (Conversation ID: 1, Members: 2)
Processing: Another Agency (ID: 5)... ✓ Created (Conversation ID: 2, Members: 5)
--------------------------------------------------
Summary:
  Created: 2
  Already existing: 0
  Errors: 0

Done!
```

### 3. Verify Database Migration

```sql
-- Check conversations table
SELECT * FROM conversations LIMIT 5;

-- Check participants
SELECT * FROM conversation_participants LIMIT 10;

-- Verify messages have conversation_id
SELECT id, conversation_id, sender_id, message 
FROM messages 
WHERE conversation_id IS NOT NULL 
LIMIT 5;

-- Check indexes
SHOW INDEX FROM messages;
SHOW INDEX FROM conversations;
SHOW INDEX FROM conversation_participants;
```

## 🧪 Complete Testing Checklist

### Phase 1: Basic Functionality (30 mins)

#### Test 1: Direct Chat (Buyer ↔ Freelancer)
```
1. Login as Buyer
2. Navigate to a freelancer's profile or gig
3. Click "Contact Seller" or use chat.php?receiver_id=FREELANCER_ID
4. ✓ Chat interface loads
5. ✓ Send message: "Hello, I need your service"
6. ✓ Message appears in chat
7. Logout

8. Login as Freelancer
9. Go to dashboard
10. ✓ See inbox with 1 conversation
11. ✓ Unread badge shows "1"
12. Click on conversation
13. ✓ See buyer's message
14. ✓ Reply: "Sure, I'd love to help!"
15. ✓ Reply appears in chat

16. Login as Buyer again
17. ✓ See inbox with unread message
18. ✓ Open chat and see freelancer's reply
```

**Expected Results:**
- Messages send instantly
- Inbox updates automatically
- Unread counts accurate
- Chat history preserved

#### Test 2: Agency Team Chat
```
1. Login as Agency owner
2. Go to agency dashboard
3. ✓ See "Team Chat" card with gradient background
4. Click "Open Team Chat"
5. ✓ Chat loads with "Team Chat" badge
6. ✓ Send message: "Team meeting at 3 PM"
7. Logout

8. Login as Agency member (freelancer)
9. ✓ See inbox with agency conversation
10. ✓ Conversation shows agency name with building icon
11. Click conversation
12. ✓ See team chat message
13. ✓ Reply: "I'll be there!"

14. Login as Agency owner
15. ✓ See member's reply in team chat
```

**Expected Results:**
- All team members see the same chat
- Messages visible to all participants
- Badge shows "Team Chat"
- Non-members cannot access

#### Test 3: Inbox Functionality
```
1. Create multiple conversations:
   - Buyer ↔ Freelancer A
   - Buyer ↔ Freelancer B
   - Agency team chat

2. Login as different users and verify:
   ✓ Inbox shows all relevant conversations
   ✓ Last message preview correct
   ✓ Timestamp shows "X min ago" or date
   ✓ Unread badge appears on unread conversations
   ✓ Conversations sorted by most recent
   ✓ Profile pictures display (or initials)

3. Wait 10 seconds
   ✓ Inbox auto-refreshes
   ✓ New messages appear without page reload
```

### Phase 2: Security Testing (20 mins)

#### Test 4: Access Control
```
1. Get conversation ID from agency team chat
2. Login as non-member freelancer
3. Try to access: chat.php?conversation_id=TEAM_CHAT_ID
   ✓ Should show "Access denied" or no messages
   ✓ Should NOT see team chat messages

4. Try API directly:
   curl -b cookies.txt "chat_api.php?action=get_messages&conversation_id=TEAM_CHAT_ID"
   ✓ Should return: {"success": false, "message": "Access denied"}

5. Try to send message to unauthorized conversation:
   curl -X POST -b cookies.txt chat_api.php \
     -d '{"action":"send_message","conversation_id":TEAM_CHAT_ID,"message":"hack"}'
   ✓ Should return error
   ✓ Message should NOT be saved
```

#### Test 5: Data Validation
```
1. Try invalid inputs:
   - conversation_id=-1
   - conversation_id=999999 (non-existent)
   - conversation_id="" (empty)
   - message="" (empty message)

2. Expected behavior:
   ✓ Appropriate error messages
   ✓ No database errors
   ✓ No PHP warnings/notices
```

### Phase 3: Backward Compatibility (15 mins)

#### Test 6: Legacy URL Support
```
1. Use old-style URLs:
   - chat.php?receiver_id=5
   - chat.php?seller_id=3

2. Verify:
   ✓ Chat loads correctly
   ✓ Automatically converts to conversation_id
   ✓ Messages send successfully
   ✓ All features work as expected

3. Check API with receiver_id:
   POST chat_api.php
   {"action": "send_message", "receiver_id": 5, "message": "Test"}
   
   ✓ Message sends successfully
   ✓ Creates conversation if doesn't exist
   ✓ Returns conversation_id in response
```

### Phase 4: Performance Testing (10 mins)

#### Test 7: Load Testing
```
1. Create conversation with 100+ messages
2. Open chat interface
   ✓ Loads in < 2 seconds
   ✓ Only latest 100 messages loaded
   ✓ Scroll works smoothly

3. Create user with 20+ conversations
4. Open inbox
   ✓ Loads in < 1 second
   ✓ All conversations display
   ✓ Unread counts accurate

5. Check database query performance:
   EXPLAIN SELECT ... FROM messages WHERE conversation_id = 1;
   ✓ Uses idx_messages_conversation_time index
   ✓ No full table scans
```

## 🔍 Debugging Common Issues

### Issue 1: Inbox Not Loading

**Symptoms:** Empty inbox or loading spinner forever

**Checks:**
```bash
# Check browser console
F12 > Console tab > Look for JavaScript errors

# Check PHP error log
tail -f /var/log/apache2/error.log

# Test API directly
curl http://localhost/skill_owners/chat_api.php?action=get_inbox
```

**Common causes:**
- Database migration not run
- Missing inbox.php file
- JavaScript error in main.js

### Issue 2: Messages Not Sending

**Symptoms:** "Failed to send message" error

**Checks:**
```sql
-- Verify conversation exists
SELECT * FROM conversations WHERE id = YOUR_CONV_ID;

-- Check if user is participant
SELECT * FROM conversation_participants 
WHERE conversation_id = YOUR_CONV_ID AND user_id = YOUR_USER_ID;
```

**Common causes:**
- User not added as participant
- Conversation doesn't exist
- Database foreign key constraint violation

### Issue 3: Agency Chat Not Working

**Symptoms:** Team chat button missing or not loading

**Checks:**
```php
// In agency dashboard, check if variable exists
var_dump($agencyConversationId);

// Should output: int(123) or similar
// If NULL, run: php agency_chat_init.php
```

**Common causes:**
- Agency conversation not created
- Agency member not added as participant
- Incorrect agency_id in URL

### Issue 4: Unread Counts Wrong

**Symptoms:** Badge shows wrong number

**Checks:**
```sql
-- Manually verify unread count
SELECT COUNT(*) FROM messages m
INNER JOIN conversation_participants cp 
  ON m.conversation_id = cp.conversation_id
WHERE cp.user_id = YOUR_USER_ID
  AND m.sender_id != YOUR_USER_ID
  AND m.created_at > COALESCE(cp.last_read_at, '1970-01-01');
```

**Fix:**
```sql
-- Reset last_read_at for a user's conversation
UPDATE conversation_participants 
SET last_read_at = CURRENT_TIMESTAMP
WHERE user_id = YOUR_USER_ID AND conversation_id = YOUR_CONV_ID;
```

## ✅ Final Verification

After 1-2 days of testing, if everything works:

```bash
# Optional: Clean up old fields
mysql -u root -p skill_owners < cleanup_old_fields.sql

# Verify cleanup
mysql -u root -p skill_owners -e "DESCRIBE messages;"

# Expected columns: id, conversation_id, sender_id, message, created_at
# receiver_id and is_read should be GONE
```

## 🎉 Success Criteria

Your migration is successful if:

- ✅ All users can send and receive messages
- ✅ Inbox loads for all user types
- ✅ Unread counts are accurate
- ✅ Agency team chat works
- ✅ Old URLs still work
- ✅ No JavaScript errors in console
- ✅ No PHP errors in logs
- ✅ No unauthorized access possible
- ✅ Performance is acceptable (< 2s page loads)

## 📞 Support

If you encounter issues:

1. Check this guide's debugging section
2. Review error logs (PHP and JavaScript)
3. Verify database migration completed
4. Test with fresh browser session (clear cache)
5. Check file permissions (755 for folders, 644 for files)

## 🔄 Rollback Plan (Emergency)

If something goes wrong:

```bash
# 1. Stop the web server
sudo service apache2 stop

# 2. Restore database
mysql -u root -p skill_owners < backup_YYYYMMDD_HHMMSS.sql

# 3. Restore old files
cp models_backup/* models/
cp views/partials_backup/* views/partials/
cp chat.php.backup chat.php
cp chat_api.php.backup chat_api.php

# 4. Restart server
sudo service apache2 start

# 5. Verify old system works
```

---

**Document Version:** 1.0  
**Last Updated:** February 2026  
**Migration Tested On:** MySQL 5.7+, PHP 7.4+, Apache 2.4+
