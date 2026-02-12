-- Step 1: Add computed column for direct conversation key
ALTER TABLE conversations 
ADD COLUMN direct_key VARCHAR(50) DEFAULT NULL AFTER type;

-- Step 2: Populate existing direct conversations
-- Use a subquery to find participants and concatenate IDs in normalized order
UPDATE conversations c
SET direct_key = (
    SELECT CONCAT(LEAST(p1.user_id, p2.user_id), '_', GREATEST(p1.user_id, p2.user_id))
    FROM (
        SELECT conversation_id, user_id 
        FROM conversation_participants 
        GROUP BY conversation_id, user_id
    ) p1
    JOIN (
        SELECT conversation_id, user_id 
        FROM conversation_participants 
        GROUP BY conversation_id, user_id
    ) p2 ON p1.conversation_id = p2.conversation_id AND p1.user_id < p2.user_id
    WHERE p1.conversation_id = c.id
)
WHERE c.type = 'direct';

-- Step 3: Add unique index (enforcing after duplicates are cleaned up)
-- We will run the cleanup script first, then apply the index manually if needed or via model logic.
-- For now, we just add the column and populate it.
