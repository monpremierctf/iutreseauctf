pid="$(ps e | grep 'python3 ctf-monitor.py' | grep -v 'grep' | cut -d' ' -f1)"
echo "Killing $pid"
kill -9 $pid 