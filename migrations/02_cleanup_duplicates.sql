-- Identify and merge duplicate direct conversations
-- This script is SAFE: it only merges messages and participants, then deletes redundancy.

-- 1. Identify pairs with multiple conversations
CREATE TEMPORARY TABLE duplicate_conversations (
    user1_id INT,
    user2_id INT,
    keep_conv_id INT,
    merged_conv_ids TEXT
);

INSERT INTO duplicate_conversations (user1_id, user2_id, keep_conv_id, merged_conv_ids)
SELECT 
    LEAST(cp1.user_id, cp2.user_id) as u1,
    GREATEST(cp1.user_id, cp2.user_id) as u2,
    MIN(c.id) as keep_id,
    GROUP_CONCAT(c.id) as all_ids
FROM conversations c
INNER JOIN conversation_participants cp1 ON c.id = cp1.conversation_id
INNER JOIN conversation_participants cp2 ON c.id = cp2.conversation_id AND cp1.user_id < cp2.user_id
WHERE c.type = 'direct'
GROUP BY u1, u2
HAVING COUNT(c.id) > 1;

-- 2. Move messages to the 'keep' conversation
UPDATE messages m
JOIN duplicate_conversations dc ON FIND_IN_SET(m.conversation_id, dc.merged_conv_ids)
SET m.conversation_id = dc.keep_conv_id
WHERE m.conversation_id != dc.keep_conv_id;

-- 3. Delete redundant participants (keeping only the one for the 'keep' conv)
DELETE cp
FROM conversation_participants cp
JOIN duplicate_conversations dc ON FIND_IN_SET(cp.conversation_id, dc.merged_conv_ids)
WHERE cp.conversation_id != dc.keep_conv_id;

-- 4. Delete the redundant conversations
DELETE c
FROM conversations c
JOIN duplicate_conversations dc ON FIND_IN_SET(c.id, dc.merged_conv_ids)
WHERE c.id != dc.keep_conv_id;

-- 5. Finally, enforce uniqueness with an index on direct_key
ALTER TABLE conversations ADD UNIQUE INDEX idx_direct_key (direct_key);
