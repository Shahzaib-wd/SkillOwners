<?php
/**
 * Agency Team Chat Initialization Script
 * Run this once to create team conversations for all existing agencies
 * 
 * Usage: php agency_chat_init.php
 */

require_once 'config.php';
require_once 'models/Message.php';
require_once 'models/AgencyMember.php';

$messageModel = new Message();
$memberModel = new AgencyMember();

try {
    $conn = getDBConnection();
    
    // Get all agencies
    $stmt = $conn->prepare("SELECT id, full_name, email FROM users WHERE role = 'agency' AND is_active = 1");
    $stmt->execute();
    $agencies = $stmt->fetchAll();
    
    echo "Found " . count($agencies) . " active agencies\n";
    echo str_repeat('-', 50) . "\n";
    
    $created = 0;
    $existing = 0;
    $errors = 0;
    
    foreach ($agencies as $agency) {
        $agencyId = $agency['id'];
        $agencyName = $agency['full_name'];
        
        echo "Processing: {$agencyName} (ID: {$agencyId})... ";
        
        try {
            // Check if conversation already exists
            $stmt = $conn->prepare("SELECT id FROM conversations WHERE type = 'agency_internal' AND agency_id = ?");
            $stmt->execute([$agencyId]);
            
            if ($stmt->fetch()) {
                echo "Already exists\n";
                $existing++;
                continue;
            }
            
            // Create conversation
            $conversationId = $messageModel->getOrCreateAgencyConversation($agencyId);
            
            if ($conversationId) {
                // Get team member count
                $members = $memberModel->getAgencyMembers($agencyId);
                $memberCount = count($members);
                
                echo "✓ Created (Conversation ID: {$conversationId}, Members: {$memberCount})\n";
                $created++;
            } else {
                echo "✗ Failed to create\n";
                $errors++;
            }
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
    
    echo str_repeat('-', 50) . "\n";
    echo "Summary:\n";
    echo "  Created: {$created}\n";
    echo "  Already existing: {$existing}\n";
    echo "  Errors: {$errors}\n";
    echo "\nDone!\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
