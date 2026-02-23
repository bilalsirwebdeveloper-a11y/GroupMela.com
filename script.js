// Global variables
let categories = [];
let allGroups = [];      // Saare groups store honge (approved + pending)
let approvedGroups = [];  // Sirf approved groups

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Website loaded, connecting to Firebase...');
    
    // Load categories
    loadCategories();
    
    // Load ALL groups (not just latest)
    loadAllGroups();
});

// ============================================
// LOAD ALL GROUPS FROM FIREBASE
// ============================================
function loadAllGroups() {
    database.ref('groups').on('value', (snapshot) => {
        allGroups = [];
        snapshot.forEach((childSnapshot) => {
            allGroups.push({
                id: childSnapshot.key,
                ...childSnapshot.val()
            });
        });
        
        console.log('Total groups loaded:', allGroups.length);
        
        // Filter approved groups
        approvedGroups = allGroups.filter(g => g && g.status === 'approved');
        console.log('Approved groups:', approvedGroups.length);
        
        // Update all sections
        displayFeaturedGroups();
        displayLatestGroups();
        updateCategoryCounts();
    });
}

// ============================================
// CATEGORIES FUNCTIONS
// ============================================

// Load categories from Firebase
function loadCategories() {
    database.ref('categories').on('value', (snapshot) => {
        categories = [];
        snapshot.forEach((childSnapshot) => {
            categories.push({
                id: childSnapshot.key,
                ...childSnapshot.val()
            });
        });
        
        console.log('Categories loaded:', categories.length);
        
        // Update UI
        displayCategoryPills();
        displayCategoriesGrid();
        displayFooterCategories();
        populateCategorySelect();
    });
}

// Display category pills (hero section)
function displayCategoryPills() {
    const container = document.getElementById('categoryPills');
    if (!container) return;
    
    container.innerHTML = '';
    
    categories.slice(0, 8).forEach(cat => {
        const pill = document.createElement('button');
        pill.className = 'pill';
        pill.innerHTML = `${cat.icon || '📌'} ${cat.name}`;
        pill.onclick = () => filterByCategory(cat.id);
        container.appendChild(pill);
    });
}

// Display categories grid
function displayCategoriesGrid() {
    const container = document.getElementById('categoriesGrid');
    if (!container) return;
    
    container.innerHTML = '';
    
    categories.forEach(cat => {
        const count = approvedGroups.filter(g => g && g.categoryId === cat.id).length;
        
        const card = document.createElement('button');
        card.className = 'category-card';
        card.onclick = () => filterByCategory(cat.id);
        card.innerHTML = `
            <div class="category-icon">${cat.icon || '📌'}</div>
            <h3>${cat.name}</h3>
            <p>${count} Groups</p>
        `;
        container.appendChild(card);
    });
}

// Update category counts (called when groups change)
function updateCategoryCounts() {
    displayCategoriesGrid();
}

// Display footer categories
function displayFooterCategories() {
    const container = document.getElementById('footerCategories');
    if (!container) return;
    
    container.innerHTML = '';
    
    categories.slice(0, 5).forEach(cat => {
        const li = document.createElement('li');
        li.innerHTML = `<a href="#" onclick="filterByCategory('${cat.id}')">${cat.name}</a>`;
        container.appendChild(li);
    });
}

// Populate category select in submit form
function populateCategorySelect() {
    const select = document.getElementById('category');
    if (!select) return;
    
    select.innerHTML = '<option value="">Select Category</option>';
    
    categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = `${cat.icon || '📌'} ${cat.name}`;
        select.appendChild(option);
    });
}

// ============================================
// FEATURED GROUPS FUNCTIONS
// ============================================

// Display featured groups
function displayFeaturedGroups() {
    const container = document.getElementById('featuredGroups');
    if (!container) return;
    
    const featured = approvedGroups.filter(g => g && g.featured === true).slice(0, 4);
    
    if (featured.length === 0) {
        container.innerHTML = '<div class="no-groups">No featured groups yet</div>';
        return;
    }
    
    container.innerHTML = '';
    
    featured.forEach(group => {
        const card = createGroupCard(group);
        container.appendChild(card);
    });
}

// ============================================
// LATEST GROUPS FUNCTIONS
// ============================================

// Display latest groups
function displayLatestGroups() {
    const container = document.getElementById('latestGroups');
    if (!container) return;
    
    // Sort by date (newest first) and take first 6
    const latest = [...approvedGroups]
        .sort((a, b) => new Date(b.createdAt || 0) - new Date(a.createdAt || 0))
        .slice(0, 6);
    
    if (latest.length === 0) {
        container.innerHTML = '<div class="no-groups">No groups yet. Be the first to submit!</div>';
        return;
    }
    
    container.innerHTML = '';
    
    latest.forEach(group => {
        const card = createGroupCard(group);
        container.appendChild(card);
    });
}

// ============================================
// GROUP CARD CREATION
// ============================================

// Create group card HTML
function createGroupCard(group) {
    const card = document.createElement('div');
    card.className = 'group-card';
    
    // Get category icon
    const category = categories.find(c => c && c.id === group.categoryId);
    const categoryIcon = category ? category.icon : '📌';
    
    // Format date
    const date = group.createdAt ? new Date(group.createdAt) : new Date();
    const timeAgo = getTimeAgo(date);
    
    card.innerHTML = `
        <div class="card-header">
            <span class="category-badge">${categoryIcon} ${group.category || 'General'}</span>
            ${group.featured ? '<span class="featured-badge"><i class="fas fa-crown"></i> Featured</span>' : ''}
        </div>
        <h3>${group.name}</h3>
        <p class="group-desc">${(group.description || 'No description').substring(0, 80)}${group.description && group.description.length > 80 ? '...' : ''}</p>
        <div class="group-meta">
            <span><i class="fas fa-users"></i> ${group.members || 0}+</span>
            <span><i class="fas fa-eye"></i> ${group.views || 0}</span>
            <span><i class="fas fa-clock"></i> ${timeAgo}</span>
        </div>
        <div class="card-actions">
            <button class="btn-view" onclick="viewGroup('${group.id}')">View Details</button>
            <a href="${group.link}" target="_blank" class="btn-join" onclick="incrementViews('${group.id}')">
                <i class="fab fa-whatsapp"></i> Join
            </a>
        </div>
    `;
    
    return card;
}

// ============================================
// SUBMIT GROUP FORM
// ============================================

// Submit new group
function submitGroup(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    const categoryId = document.getElementById('category').value;
    const category = categories.find(c => c && c.id === categoryId);
    
    if (!category) {
        showFormMessage('Please select a category', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Group';
        return;
    }
    
    const groupData = {
        name: document.getElementById('group_name').value,
        link: document.getElementById('whatsapp_link').value,
        categoryId: categoryId,
        category: category.name,
        language: document.getElementById('language').value,
        description: document.getElementById('description').value || '',
        members: 0,
        views: 0,
        featured: false,
        status: 'pending',
        submittedBy: 'user',
        submitterEmail: document.getElementById('submitter_email').value || 'anonymous',
        createdAt: new Date().toISOString()
    };
    
    // Validate WhatsApp link
    if (!groupData.link.includes('chat.whatsapp.com')) {
        showFormMessage('Please enter a valid WhatsApp group link', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Group';
        return;
    }
    
    // Save to Firebase
    const newGroupRef = database.ref('groups').push();
    groupData.id = newGroupRef.key;
    
    newGroupRef.set(groupData)
        .then(() => {
            showFormMessage('Group submitted successfully! It will appear after admin approval.', 'success');
            document.getElementById('groupForm').reset();
        })
        .catch((error) => {
            showFormMessage('Error: ' + error.message, 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Group';
        });
}

// Show form message
function showFormMessage(message, type) {
    const msgDiv = document.getElementById('formMessage');
    msgDiv.style.display = 'block';
    msgDiv.className = `alert ${type}`;
    msgDiv.textContent = message;
    
    setTimeout(() => {
        msgDiv.style.display = 'none';
    }, 5000);
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Increment views when someone clicks join
function incrementViews(groupId) {
    const groupRef = database.ref('groups/' + groupId);
    groupRef.transaction((group) => {
        if (group) {
            group.views = (group.views || 0) + 1;
        }
        return group;
    });
}

// View group details
function viewGroup(groupId) {
    database.ref('groups/' + groupId).once('value', (snapshot) => {
        const group = snapshot.val();
        if (group) {
            alert(`📌 Group: ${group.name}\n📂 Category: ${group.category}\n👥 Members: ${group.members || 0}\n📝 Description: ${group.description || 'No description'}`);
        }
    });
}

// Filter by category
function filterByCategory(categoryId) {
    const category = categories.find(c => c && c.id === categoryId);
    if (category) {
        const count = approvedGroups.filter(g => g && g.categoryId === categoryId).length;
        alert(`📂 ${category.name} Category\n\nTotal groups: ${count}\n\n🔍 Full category page coming soon!`);
    }
}

// Search groups
function searchGroups() {
    const query = document.getElementById('searchInput').value.toLowerCase().trim();
    if (!query) {
        alert('Please enter a search term');
        return;
    }
    
    const results = approvedGroups.filter(g => 
        (g.name && g.name.toLowerCase().includes(query)) || 
        (g.description && g.description.toLowerCase().includes(query))
    );
    
    if (results.length > 0) {
        alert(`✅ Found ${results.length} groups matching "${query}"\n\nFirst 5 results:\n${
            results.slice(0, 5).map(g => `• ${g.name}`).join('\n')
        }`);
    } else {
        alert(`❌ No groups found matching "${query}"`);
    }
}

// Get time ago string
function getTimeAgo(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    return date.toLocaleDateString();
}

// ============================================
// MOBILE MENU
// ============================================

// Mobile menu toggle
document.querySelector('.mobile-menu')?.addEventListener('click', function() {
    const navLinks = document.querySelector('.nav-links');
    if (navLinks.style.display === 'flex') {
        navLinks.style.display = 'none';
    } else {
        navLinks.style.display = 'flex';
        navLinks.style.flexDirection = 'column';
        navLinks.style.position = 'absolute';
        navLinks.style.top = '70px';
        navLinks.style.left = '0';
        navLinks.style.width = '100%';
        navLinks.style.background = 'white';
        navLinks.style.padding = '20px';
        navLinks.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        navLinks.style.zIndex = '1000';
    }
});
