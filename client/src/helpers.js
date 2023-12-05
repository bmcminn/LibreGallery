

export function isEmpty(value) {
    return !!!value
}



export function lsGetItem(key) {
    let value = localStorage.getItem(key)

    if (!value) { return null }

    return JSON.parse(value)
}



export function lsSetItem(key, value) {
    const data = JSON.stringify(value)
    localStorage.setItem(key, data)
    return value
}
