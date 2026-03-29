
// 🔔 LOAD FULL NOTIFICATIONS (only runs on notifications page)
async function loadNotifications(userId, userRole) {
  const container = document.getElementById("notifications");
  if (!container) return; // important for other pages

  container.innerHTML = "<p>Loading...</p>";

  try {
    const res = await fetch("/LOSBO/config/get_notifications.php?user_id=" + userId);
    const data = await res.json();

    container.innerHTML = "";

    if (!data.length) {
      container.innerHTML = "<p>No notifications</p>";
      return;
    }

    let unreadCount = 0;

    data.forEach(n => {
      if (n.status == 0) unreadCount++;
    });

    // update navbar count
    updateNotifCount(unreadCount);

    data.forEach(n => {
      const div = document.createElement("div");

      div.style.padding = "10px";
      div.style.borderBottom = "1px solid #ddd";
      div.style.cursor = "pointer";

      // highlight unread
      if (n.status == 0) {
        div.style.backgroundColor = "#4169E1";
        div.style.fontWeight = "bold";
        div.style.color = "white";
      }

      div.innerHTML = `
        <p style="margin:0;">
          <strong>${n.sender_name}</strong>: ${n.message}
        </p>
        <small style="color:${n.status == 0 ? '#fff' : 'gray'};">
          ${formatDate(n.created_at)}
        </small>
      `;

      // click → mark read + redirect
      div.onclick = async () => {

        if (n.status == 0) {
          await fetch("/LOSBO/config/mark_read.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${n.id}`
          });
        }
        
          // ROLE-BASED REDIRECTION
          if (userRole === "provider") {
        
            if (n.booking_status === "pending") {
              window.location.href = `/LOSBO/provider/requests.php?booking_id=${n.booking_id}`;
            } 
            else if (n.booking_status === "accepted") {
              window.location.href = `/LOSBO/provider/schedule.php?booking_id=${n.booking_id}`;
            } 
            else {
              window.location.href = `/LOSBO/provider/history.php?booking_id=${n.booking_id}`;
            }
        
          } else if (userRole === "customer") {
        
              window.location.href = `/LOSBO/customer/history.php?booking_id=${n.booking_id}`;
        
          }

      };

      container.appendChild(div);
    });

  } catch (err) {
    console.error(err);
    container.innerHTML = "<p>Error loading notifications</p>";
  }
}


// 🔔 GLOBAL COUNT LOADER (works on ALL pages)
async function loadNotifCount(userId) {
  try {
    const res = await fetch(`/LOSBO/config/get_unread_count.php?user_id=${userId}`);
    const data = await res.json();

    updateNotifCount(data.count);

  } catch (err) {
    console.error("Count error:", err);
  }
}


// 🔔 Update navbar UI
function updateNotifCount(count) {
  const countEl = document.getElementById("notifCount");

  if (countEl) {
    countEl.innerText = count > 0 ? `(${count})` : "";
  }
}


// 📅 Date formatter (unchanged)
function formatDate(dateString) {
  const date = new Date(dateString);

  return date.toLocaleString("en-IN", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit"
  });
}


// 🚀 AUTO RUN ON EVERY PAGE
window.addEventListener("DOMContentLoaded", () => {

  if (typeof userId !== "undefined") {

    // always load count
    loadNotifCount(userId);

    // refresh count every 10 sec
    setInterval(() => {
      loadNotifCount(userId);
    }, 10000);

    // if notifications page → load full list
    //loadNotifications(userId, userRole);
  }

});