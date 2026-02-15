<?php

class ChatHelper {
    /**
     * List of abusive words provided by the user.
     */
    private static $abusiveWords = [
        "idiot", "stupid", "dumb", "loser", "nonsense", "shut up", "useless", 
        "scammer", "fraud", "cheat", "liar", "fake", "pathetic", "trash", 
        "garbage", "clown", "noob", "moron", "bastard", "jerk", "toxic", 
        "cheap", "greedy", "thief", "time-waster", "rip-off", "exploit", 
        "blackmail", "threaten", "harass", "abuse", "insult", "curse", 
        "fight", "nonsense work", "worst service", "worst client", 
        "worst freelancer", "terrible", "horrible", "disgusting", "hate you", 
        "get lost", "don’t message me", "useless work", "garbage quality", 
        "refund now", "I’ll report you", "I’ll expose you", "I’ll ruin you", 
        "waste of money", "waste of time", "ridiculous", "unprofessional", 
        "arrogant", "annoying", "irritating", "shady", "sketchy", "corrupt", 
        "dishonest", "liar company", "fake profile", "spammer", "blocked", 
        "boycott", "nonsense platform", "worst platform", "cheating platform", 
        "scam website", "trash service", "useless support", "stupid idea", 
        "brainless", "crazy", "psycho", "mad person", "dumb project", 
        "zero skill", "incompetent", "careless", "rude", "disrespectful", 
        "offensive", "abusive", "threatening", "harassing", "bullying", 
        "insulting", "humiliating", "aggressive", "hostile", "toxic behavior", 
        "bad attitude", "drama", "conflict", "fight back", "argument", 
        "abusive language", "policy violation"
    ];

    /**
     * Checks if a text contains any abusive words using case-insensitive matching.
     * 
     * @param string $text The text to check.
     * @return bool True if abusive words are found, false otherwise.
     */
    public static function isAbusive($text) {
        if (empty($text)) return false;
        
        $lowerText = strtolower($text);
        foreach (self::$abusiveWords as $word) {
            // Use word boundary check to avoid partial matches (e.g., "liar" in "familiar")
            // except for multi-word phrases or special characters.
            if (strpos($lowerText, strtolower($word)) !== false) {
                return true;
            }
        }
        return false;
    }
}
