function showReminderDetails(log) {
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('modalContent');

    let responseData = '';
    try {
        if (log.response_data) {
            const parsed = JSON.parse(log.response_data);
            responseData = JSON.stringify(parsed, null, 2);
        }
    } catch (e) {
        responseData = log.response_data || 'لا توجد بيانات';
    }

    content.innerHTML = `
        <div class="space-y-4">
            <div>
                <strong>نوع التذكير:</strong> ${log.reminder_type}
            </div>
            <div>
                <strong>وقت الإرسال:</strong> ${log.created_at}
            </div>
            <div>
                <strong>كود HTTP:</strong> ${log.http_code}
            </div>
            ${log.custom_message ? `
            <div>
                <strong>رسالة مخصصة:</strong>
                <div class="bg-gray-100 p-3 rounded mt-2">${log.custom_message}</div>
            </div>
            ` : ''}
            <div>
                <strong>استجابة الخادم:</strong>
                <pre class="bg-gray-100 p-3 rounded mt-2 text-xs overflow-x-auto">${responseData}</pre>
            </div>
        </div>
    `;

    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
