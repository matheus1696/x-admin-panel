window.Mask = {
    cpf(value = '') {
        value = value.replace(/\D/g, '').slice(0, 11)
        value = value.replace(/(\d{3})(\d)/, '$1.$2')
        value = value.replace(/(\d{3})(\d)/, '$1.$2')
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2')
        return value
    },

    cnpj(value = '') {
        value = value.replace(/\D/g, '').slice(0, 14)
        value = value.replace(/^(\d{2})(\d)/, '$1.$2')
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2')
        value = value.replace(/(\d{4})(\d)/, '$1-$2')
        return value
    },

    phone(value = '') {
    value = value.replace(/\D/g, '').slice(0, 11)

        // DDD
        if (value.length <= 2) { return value.replace(/(\d{2})/, '($1') }

        // Até 6 dígitos após DDD
        if (value.length <= 6) { return value.replace(/(\d{2})(\d+)/, '($1) $2') }

        // Telefone fixo → 8 dígitos
        if (value.length <= 10) { return value.replace( /(\d{2})(\d{4})(\d+)/, '($1) $2-$3' )}

        // Celular → 9 dígitos
        return value.replace( /(\d{2})(\d{5})(\d+)/, '($1) $2-$3' )
    },

    registration(value = '') {
        value = value.replace(/\D/g, '').slice(0, 8)

        if (value.length > 7) {
            return value.replace(/^(\d{2})(\d{3})(\d+)/, '$1.$2.$3')
        }

        return value.replace(/^(\d{1})(\d{3})(\d+)/, '$1.$2.$3')
    },

    matriculation(value = '') {
        value = value.replace(/\D/g, '').slice(0, 6)
        return value.replace(/^(\d{2})(\d{3})(\d+)/, '$1.$2-$3')
    },

    crm(value = '') {
        value = value.replace(/\D/g, '').slice(0, 6)

        if (value.length <= 3) return value
        if (value.length === 4) return value.replace(/^(\d)(\d+)/, '$1.$2')
        if (value.length === 5) return value.replace(/^(\d{2})(\d+)/, '$1.$2')

        return value.replace(/^(\d{3})(\d+)/, '$1.$2')
    },

    money(value = '') {
        value = value.replace(/\D/g, '')

        const number = (parseInt(value, 10) / 100).toFixed(2)
        return number
            .replace('.', ',')
            .replace(/\B(?=(\d{3})+(?!\d))/g, '.')
    },

    cpfCnpj(value = '') {
        value = value.replace(/\D/g, '')

        if (value.length <= 11) {
            return Mask.cpf(value)
        }

        return Mask.cnpj(value)
    },

    cep(value = '') {
        value = value.replace(/\D/g, '').slice(0, 8)
        value = value.replace(/(\d{5})(\d)/, '$1-$2')
        return value
    }
}

document.addEventListener('input', (event) => {
    const input = event.target
    const maskName = input.dataset.mask

    if (!maskName || !window.Mask?.[maskName]) return

    const cursor = input.selectionStart
    const oldLength = input.value.length

    input.value = window.Mask[maskName](input.value)

    const newLength = input.value.length
    const diff = newLength - oldLength

    input.setSelectionRange(cursor + diff, cursor + diff)
})

