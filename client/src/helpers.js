


export function lsGetItem(key) {
    let value = localStorage.getItem(key)

    if (!value) { return null }

    return JSON.parse(value)
}

export function lsSetItem(key, value) {
    value = JSON.stringify(value)

    return localStorage.setItem(key, value)
}
