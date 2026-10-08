/* Smart Dustbin — frontend application */
(function () {
  'use strict';

  var BASE = window.BASE_URL || '';

  function api(url, opts) {
    opts = opts || {};
    opts.headers = Object.assign({ 'Accept': 'application/json' }, opts.headers || {});
    if (opts.body && !(opts.body instanceof FormData) && typeof opts.body === 'object') {
      opts.headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(opts.body);
    }
    return fetch(BASE + url, opts).then(function (res) {
      return res.json().catch(function () { return Promise.reject(new Error('Invalid server response')); })
        .then(function (data) {
          if (!res.ok) {
            var err = new Error(data.error || 'Request failed');
            err.data = data;
            throw err;
          }
          return data;
        });
    });
  }

  function $(id) { return document.getElementById(id); }
  function setText(id, text) { var el = $(id); if (el) el.textContent = text; }

  function setStatusText(id, value, okValue) {
    var el = $(id);
    if (!el) return;
    el.textContent = value;
    el.classList.remove('status-ok', 'status-off');
    el.classList.add(value === okValue ? 'status-ok' : 'status-off');
  }

  /* ---------------- Live status (topbar + dashboard) ---------------- */
  function initLiveStatus() {
    if (!$('live-dot') && !$('recent-body')) return;
    pollStatus();
    setInterval(pollStatus, 5000);
  }

  function pollStatus() {
    api('/api/dashboard.php').then(renderDashboard).catch(function (err) {
      setLive('offline', 'Connection lost');
      console.error(err);
    });
  }

  function setLive(state, text) {
    var dot = $('live-dot'), txt = $('live-text');
    if (dot) dot.className = 'live-dot ' + state;
    if (txt) txt.textContent = text;
  }

  /* ---------------- Dashboard rendering ---------------- */
  function renderDashboard(data) {
    var sys = data.system || {};

    if (sys.arduino_status === 'online') {
      setLive('online', '● System ONLINE');
    } else {
      setLive('offline', '● SYSTEM OFFLINE');
    }

    var statusBadge = $('sys-status-dot');
    if (statusBadge) {
      statusBadge.className = 'badge ' + (sys.arduino_status === 'online' ? 'badge-online' : 'badge-critical');
      statusBadge.textContent = sys.arduino_status === 'online' ? '● ONLINE' : '● OFFLINE';
    }
    setText('sys-last-updated', 'Last communication: ' + (sys.last_updated_ago || 'Never'));

    var clsEl = $('sys-classification');
    if (clsEl) {
      var cls = sys.current_classification && sys.current_classification !== 'NONE' ? sys.current_classification : '—';
      clsEl.textContent = cls;
      clsEl.className = 'stat-value' + (cls === 'WET' ? ' stat-wet' : (cls === 'DRY' ? ' stat-dry' : ''));
    }

    var t = data.today || {};
    setText('today-total', t.total != null ? t.total : 0);
    setText('today-wet', t.wet != null ? t.wet : 0);
    setText('today-dry', t.dry != null ? t.dry : 0);
    setText('unread-alerts', data.unread_alerts != null ? data.unread_alerts : 0);

    // Gauges
    if (data.bins) {
      renderGauge('wet', data.bins.wet);
      renderGauge('dry', data.bins.dry);
    }

    // LEDs
    var g = $('led-green'), y = $('led-yellow');
    if (g) g.className = 'led led-green ' + (sys.green_led === 'on' ? 'on' : '');
    if (y) y.className = 'led led-yellow ' + (sys.yellow_led === 'on' ? 'on' : '');

    // Communication
    setDot('comm-dot', 'comm-text', sys.communication_status === 'connected');
    setDot('ard-dot', 'ard-text', sys.arduino_status === 'online');

    // Recent detections
    var body = $('recent-body');
    if (body) {
      if (!data.recent || !data.recent.length) {
        body.innerHTML = '<tr><td colspan="5" class="empty">No detections yet.</td></tr>';
      } else {
        body.innerHTML = data.recent.map(function (r) {
          return '<tr>' +
            '<td>' + esc(r.date) + '</td>' +
            '<td>' + esc(r.time) + '</td>' +
            '<td><span class="pill pill-' + esc(r.classification) + '">' + esc(r.classification) + '</span></td>' +
            '<td>' + fmtNum(r.wet_capacity) + '%</td>' +
            '<td>' + fmtNum(r.dry_capacity) + '%</td>' +
            '</tr>';
        }).join('');
      }
    }
  }

  function renderGauge(side, bin) {
    var fill = $(side + '-gauge'), gauge = $(side + '-gauge'), badge = $(side + '-badge'), txt = $(side + '-gauge-text');
    if (!gauge) return;
    var level = Math.max(0, Math.min(100, bin.level || 0));
    gauge.className = 'gauge ' + bin.state;
    if (fill) fill.style.width = level + '%';
    if (txt) txt.textContent = Math.round(level) + '%';
    if (badge) {
      badge.className = 'badge badge-' + bin.state;
      var label = bin.state === 'critical' ? 'Critical' : (bin.state === 'warning' ? 'Warning' : 'Normal');
      badge.textContent = label.toUpperCase();
    }
  }

  function setDot(dotId, textId, on) {
    var dot = $(dotId), txt = $(textId);
    if (dot) {
      dot.className = 'dot' + (on ? ' dot-green' : ' dot-red');
    }
    if (txt) txt.textContent = on ? 'Connected' : 'Disconnected';
  }

  function fmtNum(v) {
    v = parseFloat(v);
    if (isNaN(v)) return '—';
    return Math.round(v);
  }

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  /* ---------------- Login ---------------- */
  function initLogin() {
    var form = $('login-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = $('login-btn'), errBox = $('login-error');
      btn.disabled = true;
      btn.textContent = 'Logging in…';
      if (errBox) errBox.hidden = true;

      api('/api/login.php', { method: 'POST', body: {
        username: form.username.value,
        password: form.password.value
      }}).then(function (data) {
        window.location.href = data.redirect;
      }).catch(function (err) {
        btn.disabled = false;
        btn.textContent = 'Login';
        if (errBox) {
          errBox.textContent = err.message;
          errBox.hidden = false;
        }
      });
    });
  }

  /* ---------------- Analytics ---------------- */
  var distChart, hourlyChart;
  var analyticsReady = false;

  function chartColors() {
    var dark = document.documentElement.getAttribute('data-theme') === 'dark';
    return {
      grid: dark ? 'rgba(255,255,255,0.08)' : 'rgba(16,24,40,0.08)',
      tick: dark ? '#8fa0b3' : '#7a8699',
      legend: dark ? '#e6edf6' : '#1b2430'
    };
  }

  function initAnalytics() {
    if (!$('chart-distribution') && !$('chart-hourly')) return;

    var segs = document.querySelectorAll('.seg-btn[data-range]');
    var active = document.querySelector('.seg-btn[data-range].active');
    var current = active ? active.dataset.range : 'today';

    function load(range) {
      current = range;
      api('/api/analytics.php?range=' + range).then(function (d) {
        setText('an-total', d.totals.total);
        setText('an-wet', d.totals.wet);
        setText('an-dry', d.totals.dry);
        setText('an-ratio', d.totals.ratio);

        drawDistribution(d.distribution);
        drawHourly(d.hourly);
      }).catch(console.error);
    }

    function drawDistribution(dist) {
      var ctx = $('chart-distribution');
      if (!ctx) return;
      if (distChart) distChart.destroy();
      var c = chartColors();
      distChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Wet Waste', 'Dry Waste'],
          datasets: [{
data: dist.map(function (d) { return d.count; }),
          backgroundColor: ['#f59e0b', '#10b981'],
            borderWidth: 0,
            hoverOffset: 6
          }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 12 }, color: c.legend } } }
        }
      });
    }

    function drawHourly(hours) {
      var ctx = $('chart-hourly');
      if (!ctx) return;
      if (hourlyChart) hourlyChart.destroy();
      var c = chartColors();
      var labels = hours.map(function (h) {
        var hr = h.hour % 12 || 12;
        return hr + (h.hour >= 12 ? 'PM' : 'AM');
      });
      hourlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [
            { label: 'Wet', data: hours.map(function (h) { return h.wet; }), backgroundColor: '#f59e0b', borderRadius: 5 },
            { label: 'Dry', data: hours.map(function (h) { return h.dry; }), backgroundColor: '#10b981', borderRadius: 5 }
          ]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          scales: {
            x: { grid: { display: false }, ticks: { color: c.tick } },
            y: { beginAtZero: true, ticks: { precision: 0, color: c.tick }, grid: { color: c.grid } }
          },
          plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 12 }, color: c.legend } } }
        }
      });
    }

    if (!analyticsReady) {
      analyticsReady = true;
      segs.forEach(function (btn) {
        btn.addEventListener('click', function () {
          segs.forEach(function (b) { b.classList.remove('active'); });
          btn.classList.add('active');
          current = btn.dataset.range;
          load(current);
        });
      });
      window.__analyticsRange = function () { return current; };
      window.__analyticsLoad = function (range) { load(range); };
    }

    load(current);
  }

  /* ---------------- History ---------------- */
  function initHistory() {
    if (!$('history-body')) return;

    var fRange = $('f-range'), fClass = $('f-class'), fQ = $('f-q');
    var timer = null;

    function load() {
      var params = new URLSearchParams();
      params.set('range', fRange.value);
      params.set('classification', fClass.value);
      if (fQ.value.trim()) params.set('q', fQ.value.trim());

      api('/api/history.php?' + params.toString()).then(function (d) {
        var body = $('history-body');
        setText('h-summary-total', d.summary.total);
        setText('h-summary-wet', d.summary.wet);
        setText('h-summary-dry', d.summary.dry);

        if (!d.records.length) {
          body.innerHTML = '<tr><td colspan="6" class="empty">No records found.</td></tr>';
        } else {
          body.innerHTML = d.records.map(function (r) {
            return '<tr>' +
              '<td>' + esc(r.date) + '</td>' +
              '<td>' + esc(r.time) + '</td>' +
              '<td><span class="pill pill-' + esc(r.classification) + '">' + esc(r.classification) + '</span></td>' +
              '<td>' + esc(r.detected_item || '—') + '</td>' +
              '<td>' + fmtNum(r.wet_capacity) + '%</td>' +
              '<td>' + fmtNum(r.dry_capacity) + '%</td>' +
              '</tr>';
          }).join('');
        }
      }).catch(console.error);
    }

    fRange.addEventListener('change', load);
    fClass.addEventListener('change', load);
    fQ.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(load, 350);
    });

    load();
  }

  /* ---------------- Alerts ---------------- */
  function initAlerts() {
    if (!$('alerts-list')) return;

    var segs = document.querySelectorAll('.seg-btn[data-status]');
    var current = 'all';

    function load(status) {
      api('/api/alerts.php?status=' + status).then(function (d) {
        var list = $('alerts-list');
        setText('unread-alerts', d.unread);
        if (!d.alerts.length) {
          list.innerHTML = '<p class="empty">No alerts.</p>';
          return;
        }
        list.innerHTML = d.alerts.map(function (a) {
          return '<div class="alert-item ' + (a.status === 'unread' ? 'unread' : '') + '">' +
            '<span class="alert-type ' + esc(a.type) + '">' + esc(a.type) + '</span>' +
            '<div class="alert-body">' +
              '<strong>' + esc(a.message) + '</strong>' +
              '<span>' + esc(a.created_at) + '</span>' +
            '</div>' +
            (a.status === 'unread' ? '<button class="btn btn-outline" data-id="' + a.id + '">Mark read</button>' : '') +
            '</div>';
        }).join('');

        Array.prototype.forEach.call(list.querySelectorAll('[data-id]'), function (btn) {
          btn.addEventListener('click', function () {
            api('/api/alerts-marker.php', { method: 'POST', body: { id: parseInt(btn.dataset.id, 10) } })
              .then(function () { load(current); });
          });
        });
      }).catch(console.error);
    }

    segs.forEach(function (btn) {
      btn.addEventListener('click', function () {
        segs.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        current = btn.dataset.status;
        load(current);
      });
    });

    var markAll = $('mark-all');
    if (markAll) markAll.addEventListener('click', function () {
      api('/api/alerts-marker.php', { method: 'POST', body: { id: 0 } }).then(function () { load(current); });
    });

    load(current);
  }

  /* ---------------- Settings ---------------- */
  function initSettings() {
    if (!$('settings-form') && !$('st-arduino')) return;

    api('/api/settings.php').then(function (d) {
      setStatusText('st-arduino', d.system.arduino_status, 'online');
      setStatusText('st-comm', d.system.communication_status, 'connected');
      setText('st-last', d.system.last_updated_ago);
      setText('st-wet', fmtNum(d.system.wet_capacity) + '%');
      setText('st-dry', fmtNum(d.system.dry_capacity) + '%');

      var form = $('settings-form');
      if (form) {
        form.warning_threshold.value = d.alerts.warning_threshold;
        form.critical_threshold.value = d.alerts.critical_threshold;
      }
    }).catch(console.error);

    var form = $('settings-form');
    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        api('/api/settings.php', { method: 'POST', body: {
          _token: form._token.value,
          warning_threshold: form.warning_threshold.value,
          critical_threshold: form.critical_threshold.value
        }}).then(function () {
          var msg = $('settings-msg');
          msg.textContent = 'Settings saved.';
          msg.hidden = false;
          setTimeout(function () { msg.hidden = true; }, 2500);
        }).catch(function (err) {
          var msg = $('settings-msg');
          msg.textContent = err.message;
          msg.className = 'form-error';
          msg.hidden = false;
        });
      });
    }
  }

  window.initLiveStatus = initLiveStatus;
  window.__sdapi = api;

  /* ---------------- Theme toggle ---------------- */
  function initTheme() {
    var btn = $('btn-theme');
    if (btn) {
      setThemeIcon();
      btn.addEventListener('click', function () {
        var dark = document.documentElement.getAttribute('data-theme') === 'dark';
        applyTheme(dark ? 'light' : 'dark');
      });
    }

    var opts = document.querySelectorAll('[data-theme-option]');
    Array.prototype.forEach.call(opts, function (el) {
      el.addEventListener('click', function () {
        applyTheme(el.dataset.themeOption);
      });
    });

    applyTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');
  }

  function setThemeIcon() {
    var icon = $('theme-icon');
    var dark = document.documentElement.getAttribute('data-theme') === 'dark';
    if (icon) icon.textContent = dark ? 'light_mode' : 'dark_mode';
  }

  function applyTheme(mode) {
    var dark = mode === 'dark';
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    localStorage.setItem('sd-theme', dark ? 'dark' : 'light');
    setThemeIcon();
    var opts = document.querySelectorAll('[data-theme-option]');
    Array.prototype.forEach.call(opts, function (el) {
      el.classList.toggle('active', el.dataset.themeOption === mode);
    });
    document.dispatchEvent(new CustomEvent('themechange', { detail: { theme: mode } }));
  }

  window.__setTheme = applyTheme;

  document.addEventListener('DOMContentLoaded', function () {
    initLogin();
    initAnalytics();
    initHistory();
    initAlerts();
    initSettings();
    initTheme();
    document.addEventListener('themechange', function () {
      if (distChart || hourlyChart) initAnalytics();
    });
  });
})();