@php
    /**
     * Composant de notification flash.
     *
     * Deux sources d'alimentation :
     *  1. Session flash (controllers avec redirect) — clés notify_success/error/warning/info
     *  2. Événement navigateur "notify" (Livewire : $this->dispatch('notify', type: '…', message: '…'))
     *
     * Les notifications s'empilent en haut à droite et se ferment automatiquement après 4 s.
     */
    $initial = collect([
        ['key' => 'notify_success', 'type' => 'success'],
        ['key' => 'notify_error',   'type' => 'danger'],
        ['key' => 'notify_warning', 'type' => 'warning'],
        ['key' => 'notify_info',    'type' => 'info'],
    ])
    ->filter(fn ($item) => session()->has($item['key']))
    ->map(fn ($item, $i) => [
        'id'      => $i + 1,
        'type'    => $item['type'],
        'message' => session($item['key']),
    ])
    ->values()
    ->toArray();
@endphp

<div
    x-data="{
        items: @js($initial),

        add(data) {
            const id = Date.now();
            this.items.push({ id, message: data.message, type: data.type ?? 'success' });
            setTimeout(() => this.close(id), 4000);
        },

        close(id) {
            this.items = this.items.filter(n => n.id !== id);
        },

        init() {
            this.items.forEach((n, i) => setTimeout(() => this.close(n.id), (i * 300) + 4000));
        }
    }"
    @notify.window="add($event.detail)"
    class="position-fixed"
    style="top: 75px; right: 20px; z-index: 9999;"
>
    <template x-for="item in items" :key="item.id">
        <div
            x-show="true"
            x-transition
            class="myadmin-alert myadmin-alert-icon mb-10"
            :class="'alert-' + item.type"
            style="min-width: 300px; max-width: 420px;"
        >
            <i :class="{
                'fa fa-check-circle':        item.type === 'success',
                'fa fa-times-circle':        item.type === 'danger'  || item.type === 'error',
                'fa fa-exclamation-triangle': item.type === 'warning',
                'fa fa-info-circle':         item.type === 'info'
            }"></i>
            <span x-text="item.message"></span>
            <a href="#" @click.prevent="close(item.id)" class="closed">&times;</a>
        </div>
    </template>
</div>
