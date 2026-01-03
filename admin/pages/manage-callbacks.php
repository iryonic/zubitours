<?php
// manage-callbacks.php
session_start();
require_once '../includes/connection.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Handle status updates
$message = '';
$message_type = '';

if (isset($_POST['update_status'])) {
    $lead_id = $_POST['lead_id'];
    $status = $_POST['status'];
    $admin_notes = $_POST['admin_notes'] ?? '';
    
    $stmt = $conn->prepare("UPDATE callback_leads SET status = ?, admin_notes = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $admin_notes, $lead_id);
    
    if ($stmt->execute()) {
        $message = "Lead status updated!";
        $message_type = "success";
    } else {
        $message = "Error: " . $conn->error;
        $message_type = "error";
    }
}

if (isset($_GET['delete_lead'])) {
    $lead_id = $_GET['delete_lead'];
    $conn->query("DELETE FROM callback_leads WHERE id = $lead_id");
    $message = "Lead deleted!";
    $message_type = "success";
}

// Fetch all leads
$leads = $conn->query("SELECT * FROM callback_leads ORDER BY submitted_at DESC");

// Stats
$total_leads = $conn->query("SELECT COUNT(*) as count FROM callback_leads")->fetch_assoc()['count'];
$new_leads = $conn->query("SELECT COUNT(*) as count FROM callback_leads WHERE status = 'new'")->fetch_assoc()['count'];
$contacted = $conn->query("SELECT COUNT(*) as count FROM callback_leads WHERE status = 'contacted'")->fetch_assoc()['count'];
$bot_leads = $conn->query("SELECT COUNT(*) as count FROM callback_leads WHERE is_bot = 1")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Callbacks - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <link rel="stylesheet" href="../assets/admin.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-info h3 {
            margin: 0;
            font-size: 1.8rem;
            color: #333;
        }
        .stat-info p {
            margin: 5px 0 0;
            color: #666;
        }
        .leads-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-new { background: #dcfce7; color: #166534; }
        .status-contacted { background: #dbeafe; color: #1e40af; }
        .status-closed { background: #f3f4f6; color: #374151; }
        .bot-badge { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="content">
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <i class="ri-<?php echo $message_type == 'success' ? 'check' : 'close'; ?>-circle-fill"></i>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endif; ?>
            
            <div class="section-header">
                <h1>Callback Leads</h1>
            </div>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dbeafe; color: #1e40af;">
                        <i class="ri-phone-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_leads; ?></h3>
                        <p>Total Leads</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dcfce7; color: #166534;">
                        <i class="ri-mail-unread-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $new_leads; ?></h3>
                        <p>New Leads</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fef3c7; color: #92400e;">
                        <i class="ri-user-voice-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $contacted; ?></h3>
                        <p>Contacted</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fee2e2; color: #991b1b;">
                        <i class="ri-robot-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $bot_leads; ?></h3>
                        <p>Bot Detections</p>
                    </div>
                </div>
            </div>
            
            <!-- Leads Table -->
            <div class="leads-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($lead = $leads->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $lead['id']; ?></td>
                            <td><?php echo htmlspecialchars($lead['name']); ?></td>
                            <td>
                                <a href="tel:<?php echo htmlspecialchars($lead['phone']); ?>" style="color: #2563eb;">
                                    <?php echo htmlspecialchars($lead['phone']); ?>
                                </a>
                                <?php if ($lead['is_bot']): ?>
                                    <span class="status-badge bot-badge" style="margin-left: 5px;">Bot</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($lead['submitted_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $lead['status']; ?>">
                                    <?php echo ucfirst($lead['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($lead['source_page']); ?></td>
                            <td>
                                <button onclick="openLeadModal(<?php echo $lead['id']; ?>)" 
                                        class="btn btn-sm btn-primary">
                                    <i class="ri-eye-line"></i> View
                                </button>
                                <a href="?delete_lead=<?php echo $lead['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this lead?')">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Lead Details Modal -->
    <div class="modal" id="leadModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeLeadModal()">&times;</span>
            <div class="modal-header">
                <h2>Lead Details</h2>
            </div>
            <div class="modal-body">
                <div id="lead-details-content"></div>
            </div>
        </div>
    </div>
    
    <script>
    function openLeadModal(leadId) {
        fetch(`../logic/get_lead.php?id=${leadId}`)
            .then(r => r.json())
            .then(data => {
                const content = `
                    <div class="lead-details">
                        <div class="detail-item">
                            <strong>Name:</strong> <span>${data.name}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Phone:</strong> 
                            <a href="tel:${data.phone}" style="color: #2563eb;">${data.phone}</a>
                            ${data.is_bot ? '<span class="badge bot-badge">Bot Detected</span>' : ''}
                        </div>
                        <div class="detail-item">
                            <strong>Submitted:</strong> <span>${new Date(data.submitted_at).toLocaleString()}</span>
                        </div>
                        <div class="detail-item">
                            <strong>IP Address:</strong> <span>${data.ip_address}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Source Page:</strong> <span>${data.source_page}</span>
                        </div>
                        <div class="detail-item">
                            <strong>User Agent:</strong> <span style="font-size: 0.9rem;">${data.user_agent}</span>
                        </div>
                    </div>
                    
                    <form method="POST" style="margin-top: 20px;">
                        <input type="hidden" name="lead_id" value="${data.id}">
                        <input type="hidden" name="update_status" value="1">
                        
                        <div class="form-group">
                            <label>Update Status:</label>
                            <select name="status" class="form-control">
                                <option value="new" ${data.status === 'new' ? 'selected' : ''}>New</option>
                                <option value="contacted" ${data.status === 'contacted' ? 'selected' : ''}>Contacted</option>
                                <option value="closed" ${data.status === 'closed' ? 'selected' : ''}>Closed</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Admin Notes:</label>
                            <textarea name="admin_notes" rows="3" class="form-control">${data.admin_notes || ''}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line"></i> Update
                        </button>
                    </form>
                `;
                document.getElementById('lead-details-content').innerHTML = content;
                document.getElementById('leadModal').classList.add('active');
            });
    }
    
    function closeLeadModal() {
        document.getElementById('leadModal').classList.remove('active');
    }
    </script>
</body>
</html>