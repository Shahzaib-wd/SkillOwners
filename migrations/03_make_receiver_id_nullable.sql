-- Make receiver_id nullable to support team chats and conversation-based architecture
-- This resolves the FK constraint violation when receiver_id is omitted or invalid.
ALTER TABLE messages MODIFY receiver_id INT(11) NULL;
